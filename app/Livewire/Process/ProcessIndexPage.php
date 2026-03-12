<?php

namespace App\Livewire\Process;

use App\Livewire\Traits\Modal;
use App\Livewire\Traits\WithFlashMessage;
use App\Models\Organization\OrganizationChart\OrganizationChart;
use App\Models\Organization\Workflow\Workflow;
use App\Models\Process\ProcessStatus;
use App\Services\Process\ProcessService;
use App\Validation\Process\ProcessRules;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ProcessIndexPage extends Component
{
    use AuthorizesRequests;
    use Modal;
    use WithFlashMessage;
    use WithPagination;

    protected ProcessService $processService;

    public array $filters = [
        'title' => '',
        'status' => ProcessStatus::IN_PROGRESS,
        'organization_id' => '',
        'overdue_only' => false,
        'my_sectors_only' => false,
        'perPage' => 10,
    ];

    public string $title = '';

    public ?string $description = null;

    public ?int $workflow_id = null;

    public function boot(ProcessService $processService): void
    {
        $this->processService = $processService;
    }

    public function mount(): void
    {
        $this->authorize('process.view');
    }

    public function updatedFilters(): void
    {
        $this->resetPage();
    }

    public function toggleQuickFilter(string $filter): void
    {
        if (! in_array($filter, ['overdue_only', 'my_sectors_only'], true)) {
            return;
        }

        $current = (bool) ($this->filters[$filter] ?? false);
        $this->filters[$filter] = ! $current;
        $this->resetPage();
    }

    public function create(): void
    {
        $this->authorize('process.create');
        $this->resetForm();
        $this->openModal('modal-process-create');
    }

    public function store(): void
    {
        $this->authorize('process.create');
        $data = $this->validate(ProcessRules::store());

        $process = $this->processService->open($data, (int) Auth::id());

        $this->flashSuccess(
            $process->started_at !== null
                ? 'Processo criado, setor atual definido pela primeira etapa e iniciado com sucesso.'
                : 'Processo criado com sucesso.'
        );
        $this->redirectRoute('process.show', $process->uuid, navigate: true);
    }

    public function openProcess(string $uuid): void
    {
        $this->redirectRoute('process.show', $uuid);
    }

    private function resetForm(): void
    {
        $this->reset([
            'title',
            'description',
            'workflow_id',
        ]);
        $this->resetValidation();
    }

    public function render()
    {
        $userId = (int) Auth::id();
        $processes = $this->processService->index($this->filters, $userId);
        $processIdsWithUnseenUpdates = $this->processService
            ->processIdsWithUnseenUpdates($processes->getCollection(), $userId);
        $processIdsWithOverdueCurrentStep = $this->processService
            ->processIdsWithOverdueCurrentStep($processes->getCollection());

        return view('livewire.process.process-index-page', [
            'processes' => $processes,
            'processIdsWithUnseenUpdates' => $processIdsWithUnseenUpdates,
            'processIdsWithOverdueCurrentStep' => $processIdsWithOverdueCurrentStep,
            'organizations' => OrganizationChart::query()->orderBy('title')->get(['id', 'title']),
            'workflows' => Workflow::query()->where('is_active', true)->orderBy('title')->get(['id', 'title']),
            'statuses' => $this->processService->availableStatuses(),
        ]);
    }
}
