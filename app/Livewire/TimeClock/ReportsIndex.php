<?php

namespace App\Livewire\TimeClock;

use App\Enums\TimeClock\TimeClockEntryStatus;
use App\Models\Administration\User\User;
use App\Models\TimeClock\TimeClockEntry;
use App\Services\TimeClock\TimeClockReportService;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ReportsIndex extends Component
{
    protected TimeClockReportService $reportService;

    public array $filters = [
        'dateFrom' => '',
        'dateTo' => '',
        'userId' => '',
        'status' => 'all',
    ];

    public function boot(TimeClockReportService $reportService): void
    {
        $this->reportService = $reportService;
    }

    public function mount(): void
    {
        Gate::authorize('viewReports', TimeClockEntry::class);
    }

    public function export()
    {
        Gate::authorize('export', TimeClockEntry::class);

        return $this->reportService->exportEntriesCsv($this->filters);
    }

    public function render(): View
    {
        return view('livewire.time-clock.reports-index', [
            'entriesByPeriod' => $this->reportService->entriesByPeriod($this->filters),
            'usersWithoutEntryToday' => $this->reportService->usersWithoutEntryToday(),
            'users' => User::query()->orderBy('name')->get(['id', 'name']),
            'statuses' => TimeClockEntryStatus::cases(),
        ]);
    }
}
