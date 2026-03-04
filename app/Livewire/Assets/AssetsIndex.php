<?php

namespace App\Livewire\Assets;

use App\Models\Assets\Asset;
use App\Models\Configuration\Establishment\Establishment\Department;
use App\Models\Configuration\Establishment\Establishment\Establishment;
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
        $assets = Asset::query()
            ->with(['unit', 'sector', 'invoiceItem.invoice'])
            ->withCount('events')
            ->when($this->filters['search'], function ($query): void {
                $search = trim($this->filters['search']);

                $query->where(function ($innerQuery) use ($search): void {
                    $innerQuery->where('code', 'like', '%'.$search.'%')
                        ->orWhere('description', 'like', '%'.$search.'%')
                        ->orWhere('serial_number', 'like', '%'.$search.'%')
                        ->orWhere('patrimony_number', 'like', '%'.$search.'%');
                });
            })
            ->when($this->filters['state'] !== 'all', fn ($query) => $query->where('state', $this->filters['state']))
            ->when($this->filters['unitId'] !== 'all', fn ($query) => $query->where('unit_id', (int) $this->filters['unitId']))
            ->when($this->filters['sectorId'] !== 'all', fn ($query) => $query->where('sector_id', (int) $this->filters['sectorId']))
            ->when($this->filters['invoiceUuid'] !== '', fn ($query) => $query->whereHas('invoiceItem.invoice', fn ($invoiceQuery) => $invoiceQuery->where('uuid', $this->filters['invoiceUuid'])))
            ->when($this->filters['invoiceItemId'] !== '', fn ($query) => $query->where('invoice_item_id', (int) $this->filters['invoiceItemId']))
            ->orderByDesc('id')
            ->paginate((int) $this->filters['perPage']);

        return view('livewire.assets.assets-index', [
            'assets' => $assets,
            'units' => Establishment::query()->orderBy('title')->get(),
            'sectors' => Department::query()->orderBy('title')->get(),
            'isInvoiceScoped' => $this->filters['invoiceUuid'] !== '' || $this->filters['invoiceItemId'] !== '',
        ])->layout('layouts.app');
    }
}
