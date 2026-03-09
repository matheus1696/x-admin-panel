<?php

namespace App\Livewire\Assets;

use App\Models\Assets\Asset;
use App\Models\Assets\AssetAuditCampaign;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;

class AuditCampaignIndex extends Component
{
    public function mount(): void
    {
        Gate::authorize('audit', Asset::class);
    }

    public function render(): View
    {
        $campaigns = AssetAuditCampaign::query()
            ->with(['unit', 'sector'])
            ->withCount([
                'items as pending_count' => fn ($query) => $query->where('status', 'PENDING'),
                'items as done_count' => fn ($query) => $query->where('status', '!=', 'PENDING'),
                'issues as open_issues_count' => fn ($query) => $query->where('status', 'OPEN'),
            ])
            ->orderByDesc('created_at')
            ->limit(40)
            ->get();

        return view('livewire.assets.audit-campaign-index', [
            'campaigns' => $campaigns,
        ])->layout('layouts.app');
    }
}

