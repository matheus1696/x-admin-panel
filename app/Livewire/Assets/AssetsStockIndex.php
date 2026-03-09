<?php

namespace App\Livewire\Assets;

use App\DTOs\Assets\CreateInvoiceDTO;
use App\DTOs\Assets\TransferAssetDTO;
use App\Enums\Assets\AssetState;
use App\Exceptions\Assets\AssetsValidationException;
use App\Livewire\Traits\Modal;
use App\Livewire\Traits\WithFlashMessage;
use App\Models\Administration\Supplier\Supplier;
use App\Models\Assets\Asset;
use App\Models\Assets\AssetInvoice;
use App\Models\Configuration\FinancialBlock\FinancialBlock;
use App\Models\Configuration\Establishment\Establishment\Department;
use App\Models\Configuration\Establishment\Establishment\Establishment;
use App\Services\Assets\AssetOperationService;
use App\Services\Assets\InvoiceService;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class AssetsStockIndex extends Component
{
    use Modal, WithFlashMessage, WithPagination;

    protected AssetOperationService $assetOperationService;
    protected InvoiceService $invoiceService;

    public array $filters = [
        'search' => '',
        'financialBlockId' => 'all',
        'perPage' => 10,
    ];

    public ?int $assetId = null;

    public ?int $unitId = null;

    public ?int $sectorId = null;

    public ?string $notes = null;

    public string $invoiceNumber = '';

    public ?string $invoiceSeries = null;

    public ?int $financialBlockId = null;

    public ?int $supplierId = null;

    public ?string $supplyOrder = null;

    public string $issueDate = '';

    public ?string $receivedDate = null;

    public ?string $invoiceNotes = null;

    public ?string $selectedStockItem = null;
    public ?int $selectedStockInvoiceId = null;

    public function boot(
        AssetOperationService $assetOperationService,
        InvoiceService $invoiceService,
    ): void
    {
        $this->assetOperationService = $assetOperationService;
        $this->invoiceService = $invoiceService;
    }

    public function mount(): void
    {
        Gate::authorize('viewAny', Asset::class);
    }

    public function updatedFilters(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->filters = [
            'search' => '',
            'financialBlockId' => 'all',
            'perPage' => 10,
        ];

        $this->resetPage();
    }

    public function openRelease(int $assetId): void
    {
        Gate::authorize('transfer', Asset::class);

        $asset = Asset::query()->findOrFail($assetId);

        $this->assetId = $asset->id;
        $this->unitId = null;
        $this->sectorId = null;
        $this->notes = null;

        $this->openModal('release-stock');
    }

    public function openStockItem(string $itemDescription): void
    {
        $decodedDescription = base64_decode($itemDescription, true);
        $this->selectedStockItem = $decodedDescription !== false ? $decodedDescription : $itemDescription;
        $this->selectedStockInvoiceId = null;
        $this->openModal('stock-item-list');
    }

    public function openStockItemInvoiceAssets(int $invoiceId): void
    {
        $this->selectedStockInvoiceId = $invoiceId;
        $this->openModal('stock-item-invoice-assets');
    }

    public function openInvoiceForm(): void
    {
        Gate::authorize('manageInvoices', Asset::class);

        $this->resetInvoiceForm();
        $this->openModal('invoice-form');
    }

    public function release(): void
    {
        Gate::authorize('transfer', Asset::class);

        $data = $this->validate([
            'assetId' => ['required', 'integer', 'exists:assets,id'],
            'unitId' => ['required', 'integer', 'exists:establishments,id'],
            'sectorId' => ['nullable', 'integer', 'exists:departments,id'],
            'notes' => ['nullable', 'string'],
        ]);

        try {
            $this->assetOperationService->releaseFromStock(new TransferAssetDTO(
                assetId: (int) $data['assetId'],
                unitId: (int) $data['unitId'],
                sectorId: isset($data['sectorId']) ? (int) $data['sectorId'] : null,
                actorUserId: auth()->id(),
                notes: $data['notes'] ?? null,
            ));
        } catch (AssetsValidationException $exception) {
            $this->flashWarning($exception->getMessage());

            return;
        }

        $this->flashSuccess('Ativo liberado do estoque com sucesso.');
        $this->closeModal();
        $this->reset(['assetId', 'unitId', 'sectorId', 'notes']);
    }

    public function saveInvoice()
    {
        Gate::authorize('manageInvoices', Asset::class);

        $data = $this->validate([
            'invoiceNumber' => ['required', 'string', 'max:255'],
            'invoiceSeries' => ['nullable', 'string', 'max:255'],
            'financialBlockId' => ['required', 'integer', 'exists:financial_blocks,id'],
            'supplierId' => ['required', 'integer', 'exists:suppliers,id'],
            'supplyOrder' => ['nullable', 'regex:/^\d{4,5}-\d{4}$/'],
            'issueDate' => ['required', 'date', 'before_or_equal:today'],
            'receivedDate' => ['nullable', 'date', 'before_or_equal:today'],
            'invoiceNotes' => ['nullable', 'string'],
        ]);

        $supplier = Supplier::query()->findOrFail((int) $data['supplierId']);

        $invoice = $this->invoiceService->createInvoice(new CreateInvoiceDTO(
            invoiceNumber: $data['invoiceNumber'],
            invoiceSeries: $data['invoiceSeries'],
            financialBlockId: (int) $data['financialBlockId'],
            supplierName: $supplier->title,
            supplierDocument: $supplier->document,
            supplyOrder: $data['supplyOrder'],
            issueDate: $data['issueDate'],
            receivedDate: $data['receivedDate'],
            totalAmount: 0,
            notes: $data['invoiceNotes'],
            createdUserId: auth()->id(),
        ));

        $this->flashSuccess('Nota fiscal cadastrada com sucesso.');
        return redirect()->route('assets.invoices.show', $invoice->uuid);
    }

    public function render(): View
    {
        $groupedStockItems = Asset::query()
            ->where('state', AssetState::IN_STOCK)
            ->when($this->filters['search'], function ($query): void {
                $search = trim($this->filters['search']);

                $query->where(function ($innerQuery) use ($search): void {
                    $innerQuery->where('assets.description', 'like', '%'.$search.'%')
                        ->orWhereHas('invoiceItem', fn ($invoiceItemQuery) => $invoiceItemQuery->where('description', 'like', '%'.$search.'%'));
                });
            })
            ->when(
                $this->filters['financialBlockId'] !== 'all',
                fn ($query) => $query->where('asset_invoices.financial_block_id', (int) $this->filters['financialBlockId'])
            )
            ->leftJoin('asset_invoice_items', 'asset_invoice_items.id', '=', 'assets.invoice_item_id')
            ->leftJoin('asset_invoices', 'asset_invoices.id', '=', 'asset_invoice_items.asset_invoice_id')
            ->leftJoin('financial_blocks', 'financial_blocks.id', '=', 'asset_invoices.financial_block_id')
            ->selectRaw("COALESCE(asset_invoice_items.description, assets.description, 'Sem item') as item_description")
            ->selectRaw('COUNT(*) as stock_quantity')
            ->selectRaw('COUNT(DISTINCT asset_invoices.financial_block_id) as financial_blocks_count')
            ->selectRaw('MIN(COALESCE(financial_blocks.acronym, financial_blocks.title)) as financial_block_label')
            ->groupByRaw("COALESCE(asset_invoice_items.description, assets.description, 'Sem item')")
            ->orderBy('item_description')
            ->paginate((int) $this->filters['perPage']);

        $selectedItemInvoices = $this->selectedStockItem
            ? Asset::query()
                ->join('asset_invoice_items', 'asset_invoice_items.id', '=', 'assets.invoice_item_id')
                ->join('asset_invoices', 'asset_invoices.id', '=', 'asset_invoice_items.asset_invoice_id')
                ->leftJoin('financial_blocks', 'financial_blocks.id', '=', 'asset_invoices.financial_block_id')
                ->where('assets.state', AssetState::IN_STOCK)
                ->where('asset_invoices.total_amount', '>', 0)
                ->where(function ($query): void {
                    $query->where('assets.description', $this->selectedStockItem)
                        ->orWhere('asset_invoice_items.description', $this->selectedStockItem);
                })
                ->selectRaw('asset_invoices.id as invoice_id')
                ->selectRaw('asset_invoices.invoice_number')
                ->selectRaw('asset_invoices.supply_order')
                ->selectRaw('asset_invoices.supplier_name')
                ->selectRaw('asset_invoices.total_amount')
                ->selectRaw('asset_invoices.issue_date')
                ->selectRaw('COALESCE(financial_blocks.acronym, financial_blocks.title) as financial_block_label')
                ->selectRaw('COUNT(assets.id) as stock_quantity')
                ->groupBy(
                    'asset_invoices.id',
                    'asset_invoices.invoice_number',
                    'asset_invoices.supply_order',
                    'asset_invoices.supplier_name',
                    'asset_invoices.total_amount',
                    'asset_invoices.issue_date',
                    'financial_blocks.acronym',
                    'financial_blocks.title'
                )
                ->orderByDesc('asset_invoices.issue_date')
                ->orderByDesc('asset_invoices.id')
                ->get()
            : collect();

        $selectedItemAssets = ($this->selectedStockItem && $this->selectedStockInvoiceId)
            ? Asset::query()
                ->with(['invoiceItem.invoice.financialBlock', 'unit'])
                ->where('state', AssetState::IN_STOCK)
                ->where(function ($query): void {
                    $query->where('assets.description', $this->selectedStockItem)
                        ->orWhereHas('invoiceItem', fn ($invoiceItemQuery) => $invoiceItemQuery->where('description', $this->selectedStockItem));
                })
                ->whereHas('invoiceItem.invoice', fn ($invoiceQuery) => $invoiceQuery->where('asset_invoices.id', $this->selectedStockInvoiceId))
                ->orderByDesc('assets.id')
                ->limit(100)
                ->get()
            : collect();

        $invoices = AssetInvoice::query()
            ->withCount('items')
            ->orderByDesc('issue_date')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        return view('livewire.assets.assets-stock-index', [
            'groupedStockItems' => $groupedStockItems,
            'selectedItemInvoices' => $selectedItemInvoices,
            'selectedItemAssets' => $selectedItemAssets,
            'invoices' => $invoices,
            'units' => Establishment::query()->orderBy('title')->get(),
            'sectors' => Department::query()->orderBy('title')->get(),
            'suppliers' => Supplier::query()->where('is_active', true)->orderBy('title')->get(),
            'financialBlocks' => FinancialBlock::query()->where('is_active', true)->orderBy('title')->get(),
        ])->layout('layouts.app');
    }

    private function resetInvoiceForm(): void
    {
        $this->reset([
            'invoiceNumber',
            'invoiceSeries',
            'financialBlockId',
            'supplierId',
            'supplyOrder',
            'issueDate',
            'receivedDate',
            'invoiceNotes',
        ]);

        $this->issueDate = '';
        $this->receivedDate = now()->toDateString();
    }
}
