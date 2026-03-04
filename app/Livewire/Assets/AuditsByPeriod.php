<?php

namespace App\Livewire\Assets;

use App\Models\Assets\Asset;
use App\Services\Assets\AssetsReportService;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;

class AuditsByPeriod extends Component
{
    protected AssetsReportService $assetsReportService;

    public ?string $startDate = null;

    public ?string $endDate = null;

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
        $rows = $this->reportRows()
            ->map(fn ($row): array => [$row->event_date, (int) $row->total])
            ->all();

        return $this->assetsReportService->exportCsv(
            'audits-by-period.csv',
            ['Data', 'Auditorias'],
            $rows
        );
    }

    public function render(): View
    {
        return view('livewire.assets.reports.audits-by-period', [
            'reportRows' => $this->reportRows(),
        ])->layout('layouts.app');
    }

    private function reportRows()
    {
        return $this->assetsReportService->auditsByPeriod([
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ]);
    }
}
