<?php

namespace App\Livewire\Assets;

use App\Models\Assets\Asset;
use App\Services\Assets\AssetsReportService;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;

class AssetsByState extends Component
{
    protected AssetsReportService $assetsReportService;

    public function boot(AssetsReportService $assetsReportService): void
    {
        $this->assetsReportService = $assetsReportService;
    }

    public function mount(): void
    {
        Gate::authorize('viewReports', Asset::class);
    }

    public function exportCsv()
    {
        $rows = $this->assetsReportService->assetsByState()
            ->map(fn (array $row): array => [$row['state'], $row['total']])
            ->all();

        return $this->assetsReportService->exportCsv(
            'assets-by-state.csv',
            ['Estado', 'Total'],
            $rows
        );
    }

    public function render(): View
    {
        return view('livewire.assets.reports.assets-by-state', [
            'reportRows' => $this->assetsReportService->assetsByState(),
        ])->layout('layouts.app');
    }
}
