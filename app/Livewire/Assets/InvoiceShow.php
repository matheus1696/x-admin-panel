<?php

namespace App\Livewire\Assets;

use App\DTOs\Assets\UpsertInvoiceItemDTO;
use App\Livewire\Traits\Modal;
use App\Livewire\Traits\WithFlashMessage;
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
    public ?string $itemCode = null;
    public string $description = '';
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
        $this->resetItemForm();
        $this->openModal('invoice-item-form');
    }

    public function editItem(int $itemId): void
    {
        $item = AssetInvoiceItem::query()
            ->where('asset_invoice_id', $this->invoiceId)
            ->findOrFail($itemId);

        $this->itemId = $item->id;
        $this->itemCode = $item->item_code;
        $this->description = $item->description;
        $this->quantity = (int) $item->quantity;
        $this->unitPrice = number_format((float) $item->unit_price, 2, '.', '');
        $this->totalPrice = number_format((float) $item->total_price, 2, '.', '');
        $this->brand = $item->brand;
        $this->model = $item->model;

        $this->openModal('invoice-item-form');
    }

    public function saveItem(): void
    {
        $data = $this->validate([
            'itemCode' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unitPrice' => ['required', 'numeric', 'min:0'],
            'totalPrice' => ['required', 'numeric', 'min:0'],
            'brand' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
        ]);

        $this->invoiceService->addOrUpdateItem(new UpsertInvoiceItemDTO(
            assetInvoiceId: $this->invoiceId,
            itemId: $this->itemId,
            itemCode: $data['itemCode'],
            description: $data['description'],
            quantity: (int) $data['quantity'],
            unitPrice: $data['unitPrice'],
            totalPrice: $data['totalPrice'],
            brand: $data['brand'],
            model: $data['model'],
        ));

        $this->resetItemForm();
        $this->flashSuccess(__('assets.invoices.items.messages.saved'));
        $this->closeModal();
    }

    public function deleteItem(int $itemId): void
    {
        $this->invoiceService->deleteItem($itemId);
        $this->flashSuccess(__('assets.invoices.items.messages.deleted'));
    }

    public function render(): View
    {
        $invoice = AssetInvoice::query()
            ->with(['items' => fn ($query) => $query->withCount('assets')->orderBy('id')])
            ->findOrFail($this->invoiceId);

        return view('livewire.assets.invoice-show', [
            'invoice' => $invoice,
        ])->layout('layouts.app');
    }

    private function resetItemForm(): void
    {
        $this->reset([
            'itemId',
            'itemCode',
            'description',
            'brand',
            'model',
        ]);

        $this->quantity = 1;
        $this->unitPrice = '0.00';
        $this->totalPrice = '0.00';
    }
}
