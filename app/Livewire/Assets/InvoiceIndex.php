<?php

namespace App\Livewire\Assets;

use App\Models\Assets\Asset;
use App\Models\Assets\AssetInvoice;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class InvoiceIndex extends Component
{
    use WithPagination;

    public array $filters = [
        'search' => '',
        'perPage' => 10,
    ];

    public function mount(): void
    {
        Gate::authorize('manageInvoices', Asset::class);
    }

    public function updatedFilters(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $invoices = AssetInvoice::query()
            ->when($this->filters['search'], function ($query): void {
                $search = trim($this->filters['search']);

                $query->where(function ($innerQuery) use ($search): void {
                    $innerQuery->where('invoice_number', 'like', '%'.$search.'%')
                        ->orWhere('supplier_name', 'like', '%'.$search.'%')
                        ->orWhere('supplier_document', 'like', '%'.$search.'%');
                });
            })
            ->withCount('items')
            ->orderByDesc('issue_date')
            ->orderByDesc('id')
            ->paginate((int) $this->filters['perPage']);

        return view('livewire.assets.invoice-index', [
            'invoices' => $invoices,
        ])->layout('layouts.app');
    }
}
