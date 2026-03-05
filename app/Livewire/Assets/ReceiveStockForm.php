<?php

namespace App\Livewire\Assets;

use App\DTOs\Assets\ReceiveStockDTO;
use App\Livewire\Traits\Modal;
use App\Livewire\Traits\WithFlashMessage;
use App\Models\Assets\Asset;
use App\Models\Assets\AssetInvoiceItem;
use App\Services\Assets\StockService;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;

class ReceiveStockForm extends Component
{
    use Modal, WithFlashMessage;

    protected StockService $stockService;

    public int $assetInvoiceItemId;

    public string $invoiceUuid;

    public int $quantity = 1;

    public ?string $acquiredDate = null;

    public ?string $description = null;

    public ?string $brand = null;

    public ?string $model = null;

    public function boot(StockService $stockService): void
    {
        $this->stockService = $stockService;
    }

    public function mount(int $assetInvoiceItemId): void
    {
        Gate::authorize('receiveStock', Asset::class);

        $item = AssetInvoiceItem::query()
            ->with('invoice')
            ->findOrFail($assetInvoiceItemId);

        $this->assetInvoiceItemId = $item->id;
        $this->invoiceUuid = $item->invoice->uuid;
    }

    public function open(): void
    {
        $item = $this->currentItem();

        $this->quantity = max(1, min(1, $this->remainingQuantity($item)));
        $this->acquiredDate = now()->toDateString();
        $this->description = $item->description;
        $this->brand = $item->brand;
        $this->model = $item->model;

        $this->openModal('receive-stock');
    }

    public function save()
    {
        $data = $this->validate([
            'quantity' => ['required', 'integer', 'min:1'],
            'acquiredDate' => ['nullable', 'date'],
            'description' => ['nullable', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
        ]);

        $assets = $this->stockService->receiveStock(new ReceiveStockDTO(
            invoiceItemId: $this->assetInvoiceItemId,
            quantity: (int) $data['quantity'],
            description: $data['description'],
            brand: $data['brand'],
            model: $data['model'],
            acquiredDate: $data['acquiredDate'],
            actorUserId: auth()->id(),
        ));

        $this->flashSuccess(count($assets).' ativo(s) gerado(s) no estoque com sucesso.');

        return redirect()->route('assets.invoices.show', $this->invoiceUuid);
    }

    public function render(): View
    {
        $item = $this->currentItem();

        return view('livewire.assets.receive-stock-form', [
            'item' => $item,
            'remainingQuantity' => $this->remainingQuantity($item),
        ]);
    }

    private function currentItem(): AssetInvoiceItem
    {
        return AssetInvoiceItem::query()
            ->withCount('assets')
            ->findOrFail($this->assetInvoiceItemId);
    }

    private function remainingQuantity(AssetInvoiceItem $item): int
    {
        return max(0, (int) $item->quantity - (int) $item->assets_count);
    }
}
