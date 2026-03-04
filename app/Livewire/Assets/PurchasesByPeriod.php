<?php

namespace App\Livewire\Assets;

use App\Models\Assets\Asset;
use App\Services\Assets\AssetsReportService;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;

class PurchasesByPeriod extends Component
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
            ->map(fn ($row): array => [
                $row->invoice_number,
                optional($row->issue_date)->format('Y-m-d'),
                $row->supplier_name,
                number_format((float) $row->total_amount, 2, '.', ''),
                (int) $row->items_count,
            ])->all();

        return $this->assetsReportService->exportCsv(
            'purchases-by-period.csv',
            ['Nota', 'Emissao', 'Fornecedor', 'Valor', 'Itens'],
            $rows
        );
    }

    public function render(): View
    {
        return view('livewire.assets.reports.purchases-by-period', [
            'reportRows' => $this->reportRows(),
        ])->layout('layouts.app');
    }

    private function reportRows()
    {
        return $this->assetsReportService->purchasesByPeriod([
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ]);
    }
}
