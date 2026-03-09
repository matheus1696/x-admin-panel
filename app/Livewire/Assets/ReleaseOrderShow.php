<?php

namespace App\Livewire\Assets;

use App\Models\Assets\Asset;
use App\Models\Assets\AssetReleaseOrder;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;

class ReleaseOrderShow extends Component
{
    public string $uuid;

    public function mount(string $uuid): void
    {
        Gate::authorize('transfer', Asset::class);
        $this->uuid = $uuid;
    }

    public function render(): View
    {
        $order = AssetReleaseOrder::query()
            ->with(['items', 'toUnit', 'toSector', 'releasedBy'])
            ->where('uuid', $this->uuid)
            ->firstOrFail();

        return view('livewire.assets.release-order-show', [
            'order' => $order,
        ])->layout('layouts.app');
    }
}

