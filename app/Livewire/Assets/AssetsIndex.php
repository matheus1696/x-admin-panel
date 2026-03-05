<?php

namespace App\Livewire\Assets;

use App\Models\Assets\Asset;
use App\Models\Configuration\Establishment\Establishment\Department;
use App\Models\Configuration\Establishment\Establishment\Establishment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Request;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class AssetsIndex extends Component
{
    use WithPagination;

    public array $filters = [
        'search' => '',
        'state' => 'all',
        'unitId' => 'all',
        'sectorId' => 'all',
        'invoiceUuid' => '',
        'invoiceItemId' => '',
        'perPage' => 10,
    ];

    public function mount(): void
    {
        Gate::authorize('viewAny', Asset::class);

        $this->filters['invoiceUuid'] = (string) Request::query('invoice_uuid', '');
        $this->filters['invoiceItemId'] = (string) Request::query('invoice_item_id', '');
    }

    public function updatedFilters(): void
    {
        $this->resetPage();
    }

    public function clearInvoiceFilter(): void
    {
        $this->filters['invoiceUuid'] = '';
        $this->filters['invoiceItemId'] = '';
        $this->resetPage();
    }

    public function render(): View
    {
        $query = $this->buildFilteredQuery();

        $groupedItems = (clone $query)
            ->leftJoin('asset_invoice_items', 'asset_invoice_items.id', '=', 'assets.invoice_item_id')
            ->selectRaw("COALESCE(asset_invoice_items.description, assets.description, 'Sem item') as item")
            ->selectRaw('COUNT(*) as quantity')
            ->selectRaw("SUM(CASE WHEN assets.state = 'IN_STOCK' THEN 1 ELSE 0 END) as in_stock_count")
            ->selectRaw("SUM(CASE WHEN assets.state = 'IN_USE' THEN 1 ELSE 0 END) as in_use_count")
            ->selectRaw("SUM(CASE WHEN assets.state = 'MAINTENANCE' THEN 1 ELSE 0 END) as maintenance_count")
            ->selectRaw("SUM(CASE WHEN assets.state = 'DAMAGED' THEN 1 ELSE 0 END) as damaged_count")
            ->groupByRaw("COALESCE(asset_invoice_items.description, assets.description, 'Sem item')")
            ->orderBy('item')
            ->get();

        return view('livewire.assets.assets-index', [
            'groupedItems' => $groupedItems,
            'units' => Establishment::query()->orderBy('title')->get(),
            'sectors' => Department::query()->orderBy('title')->get(),
            'isInvoiceScoped' => $this->filters['invoiceUuid'] !== '' || $this->filters['invoiceItemId'] !== '',
        ])->layout('layouts.app');
    }

    private function buildFilteredQuery(): Builder
    {
        return Asset::query()
            ->when($this->filters['search'], function ($query): void {
                $search = trim($this->filters['search']);

                $query->where(function ($innerQuery) use ($search): void {
                    $innerQuery->where('assets.code', 'like', '%'.$search.'%')
                        ->orWhere('assets.description', 'like', '%'.$search.'%')
                        ->orWhere('assets.serial_number', 'like', '%'.$search.'%')
                        ->orWhere('assets.patrimony_number', 'like', '%'.$search.'%');
                });
            })
            ->when($this->filters['state'] !== 'all', fn ($query) => $query->where('assets.state', $this->filters['state']))
            ->when($this->filters['unitId'] !== 'all', fn ($query) => $query->where('assets.unit_id', (int) $this->filters['unitId']))
            ->when($this->filters['sectorId'] !== 'all', fn ($query) => $query->where('assets.sector_id', (int) $this->filters['sectorId']))
            ->when($this->filters['invoiceUuid'] !== '', fn ($query) => $query->whereHas('invoiceItem.invoice', fn ($invoiceQuery) => $invoiceQuery->where('uuid', $this->filters['invoiceUuid'])))
            ->when($this->filters['invoiceItemId'] !== '', fn ($query) => $query->where('assets.invoice_item_id', (int) $this->filters['invoiceItemId']));
    }
}
