<?php

namespace App\Livewire\Assets;

use App\Models\Assets\Asset;
use App\Models\Assets\AssetReleaseOrder;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;

class ReleaseOrderIndex extends Component
{
    public function mount(): void
    {
        Gate::authorize('transfer', Asset::class);
    }

    public function render(): View
    {
        $releaseOrders = AssetReleaseOrder::query()
            ->with(['toUnit', 'toSector', 'releasedBy'])
            ->orderByDesc('released_at')
            ->orderByDesc('id')
            ->limit(50)
            ->get();

        return view('livewire.assets.release-order-index', [
            'releaseOrders' => $releaseOrders,
        ])->layout('layouts.app');
    }
}

