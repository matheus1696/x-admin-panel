<?php

namespace App\Livewire\Process;

use App\Livewire\Traits\WithFlashMessage;
use App\Models\Organization\OrganizationChart\OrganizationChart;
use App\Services\Process\ProcessService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ProcessDashboardPage extends Component
{
    use AuthorizesRequests;
    use WithFlashMessage;

    protected ProcessService $processService;

    public array $filters = [
        'window' => '90d',
        'organization_id' => 'all',
    ];

    public function boot(ProcessService $processService): void
    {
        $this->processService = $processService;
    }

    public function mount(): void
    {
        $this->authorize('process.dashboard.view');
    }

    public function updatedFilters(): void
    {
        $this->authorize('process.dashboard.view');
    }

    public function render()
    {
        $this->authorize('process.dashboard.view');

        return view('livewire.process.process-dashboard-page', [
            'dashboard' => $this->processService->dashboard($this->filters),
            'organizations' => OrganizationChart::query()->orderBy('title')->get(['id', 'title']),
        ]);
    }
}
