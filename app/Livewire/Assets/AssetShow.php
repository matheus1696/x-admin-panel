<?php

namespace App\Livewire\Assets;

use App\Models\Assets\Asset;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;

class AssetShow extends Component
{
    public string $uuid;

    public int $eventsToShow = 10;

    public function mount(string $uuid): void
    {
        $asset = Asset::query()->where('uuid', $uuid)->firstOrFail();

        Gate::authorize('view', $asset);

        $this->uuid = $asset->uuid;
    }

    public function loadMoreEvents(): void
    {
        $this->eventsToShow += 10;
    }

    public function render(): View
    {
        $asset = Asset::query()
            ->with(['unit', 'sector', 'invoiceItem.invoice', 'createdBy'])
            ->where('uuid', $this->uuid)
            ->firstOrFail();

        $events = $asset->events()
            ->with(['actor', 'fromUnit', 'toUnit', 'fromSector', 'toSector'])
            ->limit($this->eventsToShow)
            ->get();

        return view('livewire.assets.asset-show', [
            'asset' => $asset,
            'events' => $events,
            'totalEvents' => $asset->events()->count(),
        ])->layout('layouts.app');
    }
}
