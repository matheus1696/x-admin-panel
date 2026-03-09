<?php

namespace App\Livewire\Assets;

use App\DTOs\Assets\CreateInvoiceDTO;
use App\DTOs\Assets\UpsertInvoiceItemDTO;
use App\Livewire\Traits\Modal;
use App\Livewire\Traits\WithFlashMessage;
use App\Models\Administration\Product\Product;
use App\Models\Administration\Product\ProductMeasureUnit;
use App\Models\Administration\Supplier\Supplier;
use App\Models\Assets\Asset;
use App\Models\Assets\AssetInvoice;
use App\Models\Configuration\FinancialBlock\FinancialBlock;
use App\Services\Assets\InvoiceService;
use App\Exceptions\Assets\AssetsValidationException;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class InvoiceIndex extends Component
{
    use Modal, WithFlashMessage, WithPagination;

    protected InvoiceService $invoiceService;

    public array $filters = [
        'invoiceNumber' => '',
        'supplyOrder' => '',
        'supplierId' => 'all',
        'financialBlockId' => 'all',
        'status' => 'all',
        'perPage' => 10,
    ];

    public string $invoiceNumber = '';

    public ?int $invoiceId = null;

    public ?string $invoiceUuid = null;

    public ?string $invoiceSeries = null;

    public ?int $financialBlockId = null;

    public ?int $supplierId = null;

    public ?string $supplyOrder = null;

    public string $issueDate = '';

    public ?string $receivedDate = null;

    public ?string $notes = null;

    public ?int $viewInvoiceId = null;

    public ?int $itemProductId = null;

    public ?int $itemId = null;

    public bool $showFinalizeConfirm = false;

    public ?int $itemProductMeasureUnitId = null;

    public ?string $itemCode = null;

    public int $itemQuantity = 1;

    public string $itemUnitPrice = '0.00';

    public ?string $itemBrand = null;

    public ?string $itemModel = null;

    public function boot(InvoiceService $invoiceService): void
    {
        $this->invoiceService = $invoiceService;
    }

    public function mount(): void
    {
        Gate::authorize('manageInvoices', Asset::class);
    }

    public function updatedFilters(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->filters = [
            'invoiceNumber' => '',
            'supplyOrder' => '',
            'supplierId' => 'all',
            'financialBlockId' => 'all',
            'status' => 'all',
            'perPage' => 10,
        ];

        $this->resetPage();
    }

    public function openCreateInvoice(): void
    {
        $this->resetInvoiceForm();
        $this->openModal('invoice-form');
    }

    public function openEditInvoice(string $uuid): void
    {
        $invoice = AssetInvoice::query()->where('uuid', $uuid)->firstOrFail();

        if ((bool) $invoice->is_finalized) {
            $this->flashWarning('A nota fiscal ja foi finalizada e nao pode ser editada.');

            return;
        }

        $this->invoiceId = $invoice->id;
        $this->invoiceUuid = $invoice->uuid;
        $this->invoiceNumber = (string) $invoice->invoice_number;
        $this->invoiceSeries = $invoice->invoice_series;
        $this->financialBlockId = $invoice->financial_block_id;
        $this->supplyOrder = $invoice->supply_order;
        $this->issueDate = optional($invoice->issue_date)->toDateString() ?? '';
        $this->receivedDate = optional($invoice->received_date)->toDateString();
        $this->notes = $invoice->notes;

        $this->supplierId = Supplier::query()
            ->when($invoice->supplier_document, fn ($query) => $query->where('document', $invoice->supplier_document))
            ->where('title', $invoice->supplier_name)
            ->value('id');

        $this->openModal('invoice-form');
    }

    public function openViewInvoice(string $uuid): void
    {
        $invoice = AssetInvoice::query()->where('uuid', $uuid)->firstOrFail();

        $this->viewInvoiceId = $invoice->id;
        $this->resetItemForm();
        $this->openModal('invoice-show');
    }

    public function saveViewInvoiceItem(): void
    {
        if (! $this->viewInvoiceId) {
            return;
        }

        $invoice = AssetInvoice::query()->findOrFail($this->viewInvoiceId);

        if ((bool) $invoice->is_finalized) {
            $this->flashWarning('A nota fiscal ja foi finalizada. Nao e permitido alterar itens.');

            return;
        }

        $data = $this->validate([
            'itemProductId' => ['required', 'integer', 'exists:products,id'],
            'itemProductMeasureUnitId' => ['required', 'integer', 'exists:product_measure_units,id'],
            'itemCode' => ['nullable', 'string', 'max:255'],
            'itemQuantity' => ['required', 'integer', 'min:1'],
            'itemUnitPrice' => ['required', 'numeric', 'gt:0'],
            'itemBrand' => ['nullable', 'string', 'max:255'],
            'itemModel' => ['nullable', 'string', 'max:255'],
        ]);

        $product = Product::query()->findOrFail((int) $data['itemProductId']);
        $itemCode = isset($data['itemCode']) ? trim((string) $data['itemCode']) : '';

        $this->invoiceService->addOrUpdateItem(new UpsertInvoiceItemDTO(
            assetInvoiceId: $this->viewInvoiceId,
            itemId: $this->itemId,
            productId: (int) $data['itemProductId'],
            productMeasureUnitId: (int) $data['itemProductMeasureUnitId'],
            itemCode: $itemCode !== '' ? $itemCode : null,
            description: $product->title,
            quantity: (int) $data['itemQuantity'],
            unitPrice: $data['itemUnitPrice'],
            brand: $data['itemBrand'],
            model: $data['itemModel'],
        ));

        $this->flashSuccess($this->itemId ? 'Item atualizado na nota fiscal.' : 'Item cadastrado na nota fiscal.');
        $this->itemId = null;
        $this->resetItemForm();
    }

    public function finalizeViewInvoice(): void
    {
        if (! $this->viewInvoiceId) {
            return;
        }

        $invoice = AssetInvoice::query()->withCount('items')->findOrFail($this->viewInvoiceId);

        if (! $this->canFinalizeInvoice($invoice)) {
            $this->flashWarning('A finalizacao requer ao menos um item e valor total maior que zero.');

            return;
        }

        try {
            $this->invoiceService->finalizeInvoice($this->viewInvoiceId, auth()->id());
            $this->flashSuccess('Cadastro da nota fiscal concluido com sucesso.');
        } catch (AssetsValidationException $exception) {
            $this->flashWarning($exception->getMessage());
        }
    }

    public function openFinalizeConfirm(): void
    {
        if (! $this->viewInvoiceId) {
            return;
        }

        $invoice = AssetInvoice::query()->withCount('items')->findOrFail($this->viewInvoiceId);

        if (! $this->canFinalizeInvoice($invoice)) {
            $this->flashWarning('A finalizacao requer ao menos um item e valor total maior que zero.');

            return;
        }

        $this->showFinalizeConfirm = true;
    }

    public function cancelFinalizeConfirm(): void
    {
        $this->showFinalizeConfirm = false;
    }

    public function confirmFinalizeViewInvoice(): void
    {
        $this->showFinalizeConfirm = false;
        $this->finalizeViewInvoice();
    }

    public function editViewInvoiceItem(int $itemId): void
    {
        if (! $this->viewInvoiceId) {
            return;
        }

        $item = \App\Models\Assets\AssetInvoiceItem::query()
            ->where('asset_invoice_id', $this->viewInvoiceId)
            ->findOrFail($itemId);

        $this->itemId = $item->id;
        $this->itemProductId = $item->product_id;
        $this->itemProductMeasureUnitId = $item->product_measure_unit_id;
        $this->itemCode = $item->item_code;
        $this->itemQuantity = (int) $item->quantity;
        $this->itemUnitPrice = number_format((float) $item->unit_price, 2, '.', '');
        $this->itemBrand = $item->brand;
        $this->itemModel = $item->model;
    }

    public function cancelEditViewInvoiceItem(): void
    {
        $this->itemId = null;
        $this->resetItemForm();
    }

    public function deleteViewInvoiceItem(int $itemId): void
    {
        if (! $this->viewInvoiceId) {
            return;
        }

        $item = \App\Models\Assets\AssetInvoiceItem::query()
            ->where('asset_invoice_id', $this->viewInvoiceId)
            ->findOrFail($itemId);

        $this->invoiceService->deleteItem($item->id);
        $this->flashSuccess('Item removido da nota fiscal.');

        if ($this->itemId === $item->id) {
            $this->itemId = null;
            $this->resetItemForm();
        }
    }

    public function saveInvoice()
    {
        $data = $this->validate([
            'invoiceNumber' => ['required', 'string', 'max:255'],
            'invoiceSeries' => ['nullable', 'string', 'max:255'],
            'financialBlockId' => ['required', 'integer', 'exists:financial_blocks,id'],
            'supplierId' => ['required', 'integer', 'exists:suppliers,id'],
            'supplyOrder' => ['nullable', 'regex:/^\d{4,5}-\d{4}$/'],
            'issueDate' => ['required', 'date', 'before_or_equal:today'],
            'receivedDate' => ['nullable', 'date', 'before_or_equal:today'],
            'notes' => ['nullable', 'string'],
        ]);

        $supplier = Supplier::query()->findOrFail((int) $data['supplierId']);

        $dto = new CreateInvoiceDTO(
            invoiceNumber: $data['invoiceNumber'],
            invoiceSeries: $data['invoiceSeries'],
            financialBlockId: (int) $data['financialBlockId'],
            supplierName: $supplier->title,
            supplierDocument: $supplier->document,
            supplyOrder: $data['supplyOrder'],
            issueDate: $data['issueDate'],
            receivedDate: $data['receivedDate'],
            totalAmount: 0,
            notes: $data['notes'],
            createdUserId: auth()->id(),
        );

        $invoice = $this->invoiceId
            ? $this->invoiceService->updateInvoice($this->invoiceId, $dto)
            : $this->invoiceService->createInvoice($dto);

        $this->flashSuccess($this->invoiceId ? 'Nota fiscal atualizada com sucesso.' : 'Nota fiscal cadastrada com sucesso.');
        $this->closeModal();
        $this->resetInvoiceForm();
    }

    public function render(): View
    {
        $viewInvoice = $this->viewInvoiceId
            ? AssetInvoice::query()
                ->with('financialBlock')
                ->with(['items' => fn ($query) => $query
                    ->with(['product', 'measureUnit'])
                    ->withCount('assets')
                    ->orderBy('id'),
                ])
                ->find($this->viewInvoiceId)
            : null;

        $invoices = AssetInvoice::query()
            ->when($this->filters['invoiceNumber'], fn ($query) => $query->where('invoice_number', 'like', '%'.trim((string) $this->filters['invoiceNumber']).'%'))
            ->when($this->filters['supplyOrder'], fn ($query) => $query->where('supply_order', 'like', '%'.trim((string) $this->filters['supplyOrder']).'%'))
            ->when($this->filters['status'] !== 'all', function ($query): void {
                if ($this->filters['status'] === 'finalized') {
                    $query->where('is_finalized', true);

                    return;
                }

                $query->where('is_finalized', false);
            })
            ->when($this->filters['supplierId'] !== 'all', function ($query): void {
                $supplierDocument = Supplier::query()->whereKey((int) $this->filters['supplierId'])->value('document');

                if ($supplierDocument !== null) {
                    $query->where('supplier_document', $supplierDocument);
                }
            })
            ->when($this->filters['financialBlockId'] !== 'all', fn ($query) => $query->where('financial_block_id', (int) $this->filters['financialBlockId']))
            ->withCount('items')
            ->with('financialBlock:id,acronym,title')
            ->orderByDesc('issue_date')
            ->orderByDesc('id')
            ->paginate((int) $this->filters['perPage']);

        return view('livewire.assets.invoice-index', [
            'invoices' => $invoices,
            'viewInvoice' => $viewInvoice,
            'products' => Product::query()
                ->whereHas('department', fn ($query) => $query->where('code', 'PATRIMONIO'))
                ->orderBy('title')
                ->get(),
            'measureUnits' => ProductMeasureUnit::query()->orderBy('title')->get(),
            'suppliers' => Supplier::query()->where('is_active', true)->orderBy('title')->get(),
            'financialBlocks' => FinancialBlock::query()->where('is_active', true)->orderBy('title')->get(),
        ])->layout('layouts.app');
    }

    private function resetInvoiceForm(): void
    {
        $this->reset([
            'invoiceId',
            'invoiceUuid',
            'invoiceNumber',
            'invoiceSeries',
            'financialBlockId',
            'supplierId',
            'supplyOrder',
            'issueDate',
            'receivedDate',
            'notes',
        ]);

        $this->issueDate = '';
        $this->receivedDate = now()->toDateString();
    }

    public function closeModal()
    {
        $this->viewInvoiceId = null;
        $this->showFinalizeConfirm = false;
        $this->resetItemForm();
        $this->modalKey = null;
        $this->showModal = false;
        $this->resetValidation();
    }

    private function resetItemForm(): void
    {
        $this->reset([
            'itemId',
            'itemProductId',
            'itemProductMeasureUnitId',
            'itemCode',
            'itemBrand',
            'itemModel',
        ]);

        $this->itemQuantity = 1;
        $this->itemUnitPrice = '0.00';
    }

    private function canFinalizeInvoice(AssetInvoice $invoice): bool
    {
        return ! (bool) $invoice->is_finalized
            && (int) ($invoice->items_count ?? 0) > 0
            && (float) $invoice->total_amount > 0;
    }
}
