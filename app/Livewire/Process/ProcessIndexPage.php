<?php

namespace App\Livewire\Process;

use App\Enums\Process\ProcessStatus;
use App\Livewire\Traits\Modal;
use App\Livewire\Traits\WithFlashMessage;
use App\Models\Organization\OrganizationChart\OrganizationChart;
use App\Models\Organization\Workflow\Workflow;
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
        'status' => 'all',
        'organization_id' => '',
        'perPage' => 10,
    ];

    public ?int $processId = null;
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
        $this->closeModal();
    }

    public function openProcess(string $uuid): void
    {
        $this->redirectRoute('process.show', $uuid);
    }

    private function resetForm(): void
    {
        $this->reset([
            'processId',
            'title',
            'description',
            'workflow_id',
        ]);
        $this->resetValidation();
    }

    public function render()
    {
        $processes = $this->processService->index($this->filters);

        return view('livewire.process.process-index-page', [
            'processes' => $processes,
            'organizations' => OrganizationChart::query()->orderBy('title')->get(['id', 'title']),
            'workflows' => Workflow::query()->where('is_active', true)->orderBy('title')->get(['id', 'title']),
            'statuses' => ProcessStatus::cases(),
        ]);
    }
}
