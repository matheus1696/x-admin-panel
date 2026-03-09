<?php

namespace App\Livewire\Assets;

use App\Enums\Assets\AssetState;
use App\Models\Assets\Asset;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Request;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class GlobalItemAssetsIndex extends Component
{
    use WithPagination;

    public string $item = '';

    public int $perPage = 25;

    public function mount(): void
    {
        Gate::authorize('viewAny', Asset::class);

        $this->item = trim((string) Request::query('item', ''));

        abort_if($this->item === '', 404);
    }

    public function render(): View
    {
        $assets = Asset::query()
            ->with(['unit', 'sector', 'invoiceItem.invoice'])
            ->where('assets.state', '!=', AssetState::IN_STOCK->value)
            ->where(function ($query): void {
                $query->where('assets.description', $this->item)
                    ->orWhereHas('invoiceItem', fn ($invoiceItemQuery) => $invoiceItemQuery->where('description', $this->item));
            })
            ->orderByDesc('assets.id')
            ->paginate($this->perPage);

        return view('livewire.assets.global-item-assets-index', [
            'assets' => $assets,
        ])->layout('layouts.app');
    }
}
