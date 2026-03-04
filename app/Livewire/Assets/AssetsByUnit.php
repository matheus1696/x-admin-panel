<?php

namespace App\Livewire\Assets;

use App\Models\Assets\Asset;
use App\Services\Assets\AssetsReportService;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;

class AssetsByUnit extends Component
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
        $rows = $this->assetsReportService->assetsByUnit()
            ->map(fn ($row): array => [
                $row->unit_title,
                (int) $row->total_assets,
                (int) $row->in_stock_count,
                (int) $row->released_count,
                (int) $row->in_use_count,
                (int) $row->maintenance_count,
            ])->all();

        return $this->assetsReportService->exportCsv(
            'assets-by-unit.csv',
            ['Unidade', 'Total', 'Em estoque', 'Liberado', 'Em uso', 'Manutencao'],
            $rows
        );
    }

    public function render(): View
    {
        return view('livewire.assets.reports.assets-by-unit', [
            'reportRows' => $this->assetsReportService->assetsByUnit(),
        ])->layout('layouts.app');
    }
}
