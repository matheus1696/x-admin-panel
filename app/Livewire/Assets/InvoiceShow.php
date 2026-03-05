<?php

namespace App\Livewire\Assets;

use App\DTOs\Assets\UpsertInvoiceItemDTO;
use App\Exceptions\Assets\AssetsValidationException;
use App\Livewire\Traits\Modal;
use App\Livewire\Traits\WithFlashMessage;
use App\Models\Administration\Product\Product;
use App\Models\Administration\Product\ProductMeasureUnit;
use App\Models\Assets\Asset;
use App\Models\Assets\AssetInvoice;
use App\Models\Assets\AssetInvoiceItem;
use App\Services\Assets\InvoiceService;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;

class InvoiceShow extends Component
{
    use Modal, WithFlashMessage;

    protected InvoiceService $invoiceService;

    public string $uuid;
    public int $invoiceId;

    public ?int $itemId = null;
    public ?int $productId = null;
    public ?int $productMeasureUnitId = null;
    public ?string $itemCode = null;
    public int $quantity = 1;
    public string $unitPrice = '0.00';
    public string $totalPrice = '0.00';
    public ?string $brand = null;
    public ?string $model = null;

    public function boot(InvoiceService $invoiceService): void
    {
        $this->invoiceService = $invoiceService;
    }

    public function mount(string $uuid): void
    {
        Gate::authorize('manageInvoices', Asset::class);

        $invoice = AssetInvoice::query()->where('uuid', $uuid)->firstOrFail();

        $this->uuid = $invoice->uuid;
        $this->invoiceId = $invoice->id;
    }

    public function createItem(): void
    {
        if (! $this->canEditItems()) {
            return;
        }

        $this->resetItemForm();
        $this->openModal('invoice-item-form');
    }

    public function editItem(int $itemId): void
    {
        if (! $this->canEditItems()) {
            return;
        }

        $item = AssetInvoiceItem::query()
            ->where('asset_invoice_id', $this->invoiceId)
            ->findOrFail($itemId);

        $this->itemId = $item->id;
        $this->productId = $item->product_id;
        $this->productMeasureUnitId = $item->product_measure_unit_id;
        $this->itemCode = $item->item_code;
        $this->quantity = (int) $item->quantity;
        $this->unitPrice = number_format((float) $item->unit_price, 2, '.', '');
        $this->totalPrice = number_format((float) $item->total_price, 2, '.', '');
        $this->brand = $item->brand;
        $this->model = $item->model;

        $this->openModal('invoice-item-form');
    }

    public function saveItem(): void
    {
        if (! $this->canEditItems()) {
            return;
        }

        $isCreating = $this->itemId === null;

        $data = $this->validate([
            'productId' => ['required', 'integer', 'exists:products,id'],
            'productMeasureUnitId' => ['required', 'integer', 'exists:product_measure_units,id'],
            'itemCode' => ['nullable', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unitPrice' => ['required', 'numeric', 'gt:0'],
            'brand' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
        ]);

        $product = Product::query()->findOrFail((int) $data['productId']);
        $itemCode = isset($data['itemCode']) ? trim((string) $data['itemCode']) : '';

        $this->invoiceService->addOrUpdateItem(new UpsertInvoiceItemDTO(
            assetInvoiceId: $this->invoiceId,
            itemId: $this->itemId,
            productId: (int) $data['productId'],
            productMeasureUnitId: (int) $data['productMeasureUnitId'],
            itemCode: $itemCode !== '' ? $itemCode : null,
            description: $product->title,
            quantity: (int) $data['quantity'],
            unitPrice: $data['unitPrice'],
            brand: $data['brand'],
            model: $data['model'],
        ));

        $this->resetItemForm();
        $this->flashSuccess($isCreating ? 'Item cadastrado na nota fiscal.' : 'Item da nota fiscal atualizado com sucesso.');
        $this->closeModal();
    }

    public function deleteItem(int $itemId): void
    {
        if (! $this->canEditItems()) {
            return;
        }

        $this->invoiceService->deleteItem($itemId);
        $this->flashSuccess('Item da nota removido com sucesso.');
    }

    public function finalizeInvoice(): void
    {
        try {
            $this->invoiceService->finalizeInvoice($this->invoiceId, auth()->id());
            $this->flashSuccess('Finalizacao concluida: nota fiscal bloqueada para edicao e itens enviados ao estoque.');
        } catch (AssetsValidationException $exception) {
            $this->flashWarning($exception->getMessage());
        }
    }

    public function render(): View
    {
        $invoice = AssetInvoice::query()
            ->with('financialBlock')
            ->with(['items' => fn ($query) => $query
                ->with(['product', 'measureUnit'])
                ->withCount('assets')
                ->orderBy('id'),
            ])
            ->findOrFail($this->invoiceId);

        return view('livewire.assets.invoice-show', [
            'invoice' => $invoice,
            'products' => Product::query()
                ->whereHas('department', fn ($query) => $query->where('code', 'PATRIMONIO'))
                ->orderBy('title')
                ->get(),
            'measureUnits' => ProductMeasureUnit::query()->orderBy('title')->get(),
        ])->layout('layouts.app');
    }

    public function updatedQuantity(): void
    {
        $this->recalculateTotalPrice();
    }

    public function updatedUnitPrice(): void
    {
        $this->recalculateTotalPrice();
    }

    private function resetItemForm(): void
    {
        $this->reset([
            'itemId',
            'productId',
            'productMeasureUnitId',
            'itemCode',
            'brand',
            'model',
        ]);

        $this->quantity = 1;
        $this->unitPrice = '0.00';
        $this->totalPrice = '0.00';
    }

    private function recalculateTotalPrice(): void
    {
        $quantity = max(0, (int) $this->quantity);
        $unitPrice = max(0, (float) $this->unitPrice);
        $this->totalPrice = number_format($quantity * $unitPrice, 2, '.', '');
    }

    private function canEditItems(): bool
    {
        $isFinalized = (bool) AssetInvoice::query()
            ->where('id', $this->invoiceId)
            ->value('is_finalized');

        if ($isFinalized) {
            $this->flashWarning('A nota fiscal ja foi finalizada. Nao e permitido alterar itens.');
            return false;
        }

        return true;
    }
}
