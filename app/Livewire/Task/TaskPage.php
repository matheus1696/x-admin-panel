<?php

namespace App\Livewire\Task;

use App\Livewire\Traits\Modal;
use App\Livewire\Traits\WithFlashMessage;
use App\Models\Administration\Task\TaskCategory;
use App\Models\Administration\Task\TaskPriority;
use App\Models\Administration\Task\TaskStatus;
use App\Models\Administration\Task\TaskStepCategory;
use App\Models\Administration\User\User;
use App\Models\Organization\OrganizationChart\OrganizationChart;
use App\Models\Organization\Workflow\Workflow;
use App\Models\Task\Task;
use App\Models\Task\TaskHub;
use App\Models\Task\TaskStep;
use App\Services\Administration\Task\TaskStatusService;
use App\Services\Administration\Task\TaskStepStatusService;
use App\Services\Task\TaskService;
use App\Validation\Task\TaskRules;
use App\Validation\Task\TaskStepRules;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class TaskPage extends Component
{
    use Modal, WithFlashMessage, WithPagination;

    protected TaskService $taskService;

    protected TaskStatusService $taskStatusService;

    protected TaskStepStatusService $taskStepStatusesService;

    public array $filters = [
        'title' => '',
        'workflow_run_status_id' => 'all',
        'perPage' => 50,
    ];

    public Collection $users;

    public Collection $organizations;

    public Collection $taskCategories;

    public Collection $taskPriorities;

    public Collection $taskStatuses;

    public Collection $taskStepCategories;

    public Collection $taskStepStatuses;

    public Collection $workflows;

    public $taskHubId;

    public $taskId;

    public ?string $title = null;

    public ?int $user_id = null;

    public ?int $organization_id = null;

    public ?int $task_category_id = null;

    public ?int $task_priority_id = null;

    public ?int $task_status_id = null;

    public ?int $task_step_status_id = null;

    public ?int $workflow_id = null;

    public bool $isCreatingTask = false;

    public bool $isCreatingTaskStep = false;

    public ?int $selectedTaskId = null;

    public ?int $selectedTaskStepId = null;

    public ?int $member_user_id = null;

    public ?int $completedStatusId = null;

    public ?int $cancelledStatusId = null;

    public ?int $pendingKanbanTaskId = null;

    public ?int $pendingKanbanFromStatusId = null;

    public ?int $pendingKanbanToStatusId = null;

    public array $pendingKanbanSourceOrder = [];

    public array $pendingKanbanTargetOrder = [];

    public ?string $pendingKanbanReasonType = null;

    public string $kanbanReason = '';

    public string $kanbanReasonTitle = '';

    public string $kanbanCompletionComment = '';

    public ?int $stepCompletedStatusId = null;

    public ?int $stepCancelledStatusId = null;

    public ?int $pendingKanbanStepId = null;

    public ?int $pendingKanbanStepFromStatusId = null;

    public ?int $pendingKanbanStepToStatusId = null;

    public array $pendingKanbanStepSourceOrder = [];

    public array $pendingKanbanStepTargetOrder = [];

    public ?string $pendingKanbanStepReasonType = null;

    public string $kanbanStepReason = '';

    public string $kanbanStepReasonTitle = '';

    public string $kanbanStepCompletionComment = '';

    public function boot(TaskService $taskService, TaskStatusService $taskStatusService, TaskStepStatusService $taskStepStatusesService)
    {
        $this->taskService = $taskService;
        $this->taskStatusService = $taskStatusService;
        $this->taskStepStatusesService = $taskStepStatusesService;
    }

    protected function setDefaults(): void
    {
        $this->task_priority_id = $this->taskPriorities->firstWhere('is_default', true)?->id;
        $this->task_status_id = collect($this->taskStatuses)->firstWhere('is_default', true)?->id;
        $this->task_step_status_id = collect($this->taskStepStatuses)->firstWhere('is_default', true)?->id;
    }

    public function mount($uuid)
    {
        $userId = Auth::user()->id;

        $taskHub = TaskHub::query()
            ->where('uuid', $uuid)
            ->where(function ($query) use ($userId): void {
                $query->where('owner_id', $userId)
                    ->orWhereHas('members', function ($memberQuery) use ($userId): void {
                        $memberQuery->where('user_id', $userId);
                    });
            })
            ->firstOrFail();

        $this->taskHubId = $taskHub->uuid;
        $this->users = User::orderBy('name')->get();
        $this->organizations = OrganizationChart::orderBy('order')->get();
        $this->taskCategories = TaskCategory::orderBy('title')->get();
        $this->taskPriorities = TaskPriority::orderBy('level')->get();
        $this->taskStatuses = $this->taskStatusService->index();
        $this->taskStepCategories = TaskStepCategory::orderBy('title')->get();
        $this->taskStepStatuses = $this->taskStepStatusesService->index();
        $this->workflows = Workflow::orderBy('title')->get();

        $terminalStatuses = TaskStatus::query()
            ->whereIn('title', ['Concluído', 'Cancelado'])
            ->pluck('id', 'title');
        $this->completedStatusId = $terminalStatuses['Concluído'] ?? null;
        $this->cancelledStatusId = $terminalStatuses['Cancelado'] ?? null;

        $stepTerminalStatuses = \App\Models\Administration\Task\TaskStepStatus::query()
            ->whereIn('title', ['Concluída', 'Cancelada'])
            ->pluck('id', 'title');
        $this->stepCompletedStatusId = $stepTerminalStatuses['Concluída'] ?? null;
        $this->stepCancelledStatusId = $stepTerminalStatuses['Cancelada'] ?? null;
    }

    public function updatedFilters()
    {
        $this->resetPage();
    }

    public function resetForm()
    {
        $this->reset('title', 'user_id', 'task_category_id', 'task_priority_id', 'task_status_id');
    }

    // Início Formulário de Criação de Tarefa
    public function enableCreateTask()
    {
        $this->resetForm();
        $this->setDefaults();
        $this->isCreatingTask = true;
    }

    public function cancelCreateTask()
    {
        $this->resetForm();
        $this->setDefaults();
        $this->isCreatingTask = false;
    }

    public function storeTask()
    {
        $data = $this->validate(TaskRules::store());

        $this->taskService->create($this->taskHubId, $data);

        $this->isCreatingTask = false;
        $this->resetForm();
        $this->flashSuccess('Tarefa criada com sucesso.');
    }
    // Fim

    // Início Formulário de Compartilhamento de Ambiente (removido)

    // Início Formulário de Criação de Etapas
    public function enableCreateTaskStep($id)
    {
        $this->resetForm();
        $this->setDefaults();
        $this->taskId = $id;
        $this->isCreatingTaskStep = true;
    }

    public function cancelCreateTaskStep()
    {
        $this->resetForm();
        $this->setDefaults();
        $this->taskId = null;
        $this->isCreatingTaskStep = false;
    }

    public function storeStep($id, $task_hub)
    {
        $data = $this->validate(TaskStepRules::store());
        $data['task_id'] = $id;
        $data['task_hub_id'] = $task_hub;
        $data['task_status_id'] = $data['task_step_status_id'];
        $data['kanban_order'] = $this->taskService->nextStepKanbanOrder($task_hub, $data['task_status_id']);

        TaskStep::create($data);

        $this->isCreatingTaskStep = false;
        $this->resetForm();
        $this->flashSuccess('Tarefa criada com sucesso.');
    }
    // Fim

    // Início Formulário de Copia do Fluxo de Trabalho
    public function openCopyWorkflowModal(int $taskId)
    {
        $this->reset(['workflow_id']);
        $this->taskId = $taskId;

        $this->openModal('modal-copy-workflow-steps');
    }

    public function copyWorkflowSteps()
    {
        if (TaskStep::where('task_id', $this->taskId)->exists()) {
            $this->flashError('Esta tarefa já possui etapas.');

            return;
        }

        $workflow = Workflow::findOrFail($this->workflow_id);
        if ($workflow->workflowSteps->isEmpty()) {
            $this->flashError('Este fluxo não possui etapas.');

            return;
        }
        $currentDeadline = now();
        $this->setDefaults();

        $task = Task::find($this->taskId);
        $lastDeadline = null;

        foreach ($workflow->workflowSteps as $key => $step) {

            if ($step->deadline_days) {
                $currentDeadline = $currentDeadline->copy()->addDays($step->deadline_days);
            }

            $taskStep = TaskStep::create([
                'task_id' => $task->id,
                'task_hub_id' => $task->task_hub_id,
                'title' => $step->title,
                'organization_id' => $step->organization_id,
                'deadline_at' => $step->deadline_days ? $currentDeadline : null,
                'task_status_id' => $this->task_step_status_id,
                'task_priority_id' => $this->task_priority_id,
                'kanban_order' => $this->taskService->nextStepKanbanOrder($task->task_hub_id, $this->task_step_status_id),
            ]);

            $lastDeadline = $taskStep->deadline_at;
        }

        if ($lastDeadline) {
            $taskUpdate = Task::find($this->taskId);
            $taskUpdate->deadline_at = $lastDeadline;
            $taskUpdate->save();
        }

        $this->flashSuccess('Etapas copiadas com sucesso.');
        $this->closeModal();
    }
    // Fim

    // Início Informações Aside das Tarefas
    public function openAsideTask($id)
    {
        $this->selectedTaskId = $id;
    }

    public function openAsideTaskStep($id)
    {
        $this->selectedTaskStepId = $id;
    }

    public function closedAsideTask()
    {
        $this->resetPage();
        $this->selectedTaskId = null;
        $this->selectedTaskStepId = null;
    }
    // Fim

    public function render()
    {
        $taskHub = TaskHub::query()
            ->with(['members.user'])
            ->where('uuid', $this->taskHubId)
            ->firstOrFail();

        return view('livewire.task.task-page', [
            'tasks' => $this->taskService->index($this->taskHubId, $this->filters),
            'dashboard' => $this->taskService->dashboard($this->taskHubId),
            'kanbanColumns' => $this->taskService->kanban($this->taskHubId),
            'kanbanStepColumns' => $this->taskService->stepKanban($this->taskHubId),
            'taskHub' => $taskHub,
        ]);
    }

    public function moveKanbanTask(
        int $taskId,
        ?int $fromStatusId,
        ?int $toStatusId,
        array $sourceOrder,
        array $targetOrder
    ): void {
        $this->requestKanbanMove($taskId, $fromStatusId, $toStatusId, $sourceOrder, $targetOrder);
    }

    public function moveKanbanStep(
        int $stepId,
        ?int $fromStatusId,
        ?int $toStatusId,
        array $sourceOrder,
        array $targetOrder
    ): void {
        $this->requestKanbanStepMove($stepId, $fromStatusId, $toStatusId, $sourceOrder, $targetOrder);
    }

    public function addMember(): void
    {
        $data = $this->validate([
            'member_user_id' => 'required|exists:users,id',
        ]);

        $taskHub = TaskHub::query()
            ->where('uuid', $this->taskHubId)
            ->where('owner_id', Auth::user()->id)
            ->firstOrFail();

        \App\Models\Task\TaskHubMember::firstOrCreate([
            'task_hub_id' => $taskHub->id,
            'user_id' => $data['member_user_id'],
        ]);

        $this->member_user_id = null;
        $this->flashSuccess('Usuário adicionado com sucesso.');
    }

    public function removeMember(int $memberId): void
    {
        $member = \App\Models\Task\TaskHubMember::query()
            ->whereKey($memberId)
            ->whereHas('taskHub', function ($query): void {
                $query->where('uuid', $this->taskHubId)
                    ->where('owner_id', Auth::user()->id);
            })
            ->firstOrFail();

        $member->delete();

        $this->flashSuccess('Usuário removido do ambiente.');
    }

    public function requestKanbanStepMove(
        int $stepId,
        ?int $fromStatusId,
        ?int $toStatusId,
        array $sourceOrder,
        array $targetOrder
    ): void {
        $sourceOrder = array_map('intval', $sourceOrder);
        $targetOrder = array_map('intval', $targetOrder);

        $reasonType = $this->resolveStepReasonType($fromStatusId, $toStatusId);

        if ($reasonType !== null) {
            $this->pendingKanbanStepId = $stepId;
            $this->pendingKanbanStepFromStatusId = $fromStatusId;
            $this->pendingKanbanStepToStatusId = $toStatusId;
            $this->pendingKanbanStepSourceOrder = $sourceOrder;
            $this->pendingKanbanStepTargetOrder = $targetOrder;
            $this->pendingKanbanStepReasonType = $reasonType;
            $this->kanbanStepReason = '';
            $this->kanbanStepCompletionComment = '';
            $this->kanbanStepReasonTitle = $this->resolveStepReasonTitle($reasonType);

            $this->openModal('modal-kanban-step-reason');

            return;
        }

        $this->taskService->moveKanbanStep(
            $this->taskHubId,
            $stepId,
            $fromStatusId,
            $toStatusId,
            $sourceOrder,
            $targetOrder,
            null,
            null
        );
    }

    public function confirmKanbanStepMove(): void
    {
        $rules = [
            'kanbanStepReason' => ['required', 'string', 'min:3'],
        ];

        if ($this->pendingKanbanStepReasonType === 'completion') {
            $rules['kanbanStepCompletionComment'] = ['required', 'string', 'min:3'];
        }

        $data = $this->validate($rules);

        if (! $this->pendingKanbanStepId) {
            $this->closeModal();

            return;
        }

        $this->taskService->moveKanbanStep(
            $this->taskHubId,
            $this->pendingKanbanStepId,
            $this->pendingKanbanStepFromStatusId,
            $this->pendingKanbanStepToStatusId,
            $this->pendingKanbanStepSourceOrder,
            $this->pendingKanbanStepTargetOrder,
            $data['kanbanStepReason'],
            $this->pendingKanbanStepReasonType
        );

        if ($this->pendingKanbanStepReasonType === 'completion' && $data['kanbanStepCompletionComment']) {
            $this->taskService->storeStepComment(
                $this->pendingKanbanStepId,
                'Conclusão: '.$data['kanbanStepCompletionComment']
            );
        }

        $this->resetKanbanStepReason();
        $this->closeModal();
    }

    public function cancelKanbanStepMove(): void
    {
        $this->resetKanbanStepReason();
        $this->closeModal();
        $this->dispatch('$refresh');
    }

    private function resolveStepReasonType(?int $fromStatusId, ?int $toStatusId): ?string
    {
        if ($toStatusId !== null && $toStatusId === $this->stepCompletedStatusId) {
            return 'completion';
        }

        if ($toStatusId !== null && $toStatusId === $this->stepCancelledStatusId) {
            return 'cancellation';
        }

        if (
            $fromStatusId !== null
            && in_array($fromStatusId, array_filter([$this->stepCompletedStatusId, $this->stepCancelledStatusId]), true)
            && $fromStatusId !== $toStatusId
        ) {
            return 'reopen';
        }

        return null;
    }

    private function resolveStepReasonTitle(string $reasonType): string
    {
        return match ($reasonType) {
            'completion' => 'Descreva o motivo da conclusão da etapa',
            'cancellation' => 'Descreva o motivo do cancelamento da etapa',
            'reopen' => 'Descreva o motivo da reabertura da etapa',
            default => 'Descreva o motivo',
        };
    }

    private function resetKanbanStepReason(): void
    {
        $this->pendingKanbanStepId = null;
        $this->pendingKanbanStepFromStatusId = null;
        $this->pendingKanbanStepToStatusId = null;
        $this->pendingKanbanStepSourceOrder = [];
        $this->pendingKanbanStepTargetOrder = [];
        $this->pendingKanbanStepReasonType = null;
        $this->kanbanStepReason = '';
        $this->kanbanStepReasonTitle = '';
        $this->kanbanStepCompletionComment = '';
    }

    public function requestKanbanMove(
        int $taskId,
        ?int $fromStatusId,
        ?int $toStatusId,
        array $sourceOrder,
        array $targetOrder
    ): void {
        $sourceOrder = array_map('intval', $sourceOrder);
        $targetOrder = array_map('intval', $targetOrder);

        if ($this->isTerminalStatus($fromStatusId) && $this->isTerminalStatus($toStatusId) && $fromStatusId !== $toStatusId) {
            $this->flashError('Tarefas concluÃ­das ou canceladas devem voltar para um status ativo.');
            $this->dispatch('$refresh');

            return;
        }

        $reasonType = $this->resolveKanbanReasonType($fromStatusId, $toStatusId);

        if ($reasonType !== null) {
            $this->pendingKanbanTaskId = $taskId;
            $this->pendingKanbanFromStatusId = $fromStatusId;
            $this->pendingKanbanToStatusId = $toStatusId;
            $this->pendingKanbanSourceOrder = $sourceOrder;
            $this->pendingKanbanTargetOrder = $targetOrder;
            $this->pendingKanbanReasonType = $reasonType;
            $this->kanbanReason = '';
            $this->kanbanReasonTitle = $this->resolveKanbanReasonTitle($reasonType);

            $this->openModal('modal-kanban-reason');

            return;
        }

        $this->taskService->moveKanbanTask(
            $this->taskHubId,
            $taskId,
            $fromStatusId,
            $toStatusId,
            $sourceOrder,
            $targetOrder,
            null,
            null
        );
    }

    public function confirmKanbanMove(): void
    {
        $rules = [
            'kanbanReason' => ['required', 'string', 'min:3'],
        ];

        if ($this->pendingKanbanReasonType === 'completion') {
            $rules['kanbanCompletionComment'] = ['required', 'string', 'min:3'];
        }

        $data = $this->validate($rules);

        if (! $this->pendingKanbanTaskId) {
            $this->closeModal();

            return;
        }

        $this->taskService->moveKanbanTask(
            $this->taskHubId,
            $this->pendingKanbanTaskId,
            $this->pendingKanbanFromStatusId,
            $this->pendingKanbanToStatusId,
            $this->pendingKanbanSourceOrder,
            $this->pendingKanbanTargetOrder,
            $data['kanbanReason'],
            $this->pendingKanbanReasonType
        );

        if ($this->pendingKanbanReasonType === 'completion' && $data['kanbanCompletionComment']) {
            $this->taskService->storeComment(
                $this->pendingKanbanTaskId,
                ['comment' => 'ConclusÃ£o: '.$data['kanbanCompletionComment']],
                'comment'
            );
        }

        $this->resetKanbanReason();
        $this->closeModal();
    }

    public function cancelKanbanMove(): void
    {
        $this->resetKanbanReason();
        $this->closeModal();
        $this->dispatch('$refresh');
    }

    private function resolveKanbanReasonType(?int $fromStatusId, ?int $toStatusId): ?string
    {
        if ($toStatusId !== null && $toStatusId === $this->completedStatusId) {
            return 'completion';
        }

        if ($toStatusId !== null && $toStatusId === $this->cancelledStatusId) {
            return 'cancellation';
        }

        if (
            $fromStatusId !== null
            && in_array($fromStatusId, array_filter([$this->completedStatusId, $this->cancelledStatusId]), true)
            && $fromStatusId !== $toStatusId
        ) {
            return 'reopen';
        }

        return null;
    }

    private function isTerminalStatus(?int $statusId): bool
    {
        if ($statusId === null) {
            return false;
        }

        return in_array($statusId, array_filter([$this->completedStatusId, $this->cancelledStatusId]), true);
    }

    private function resolveKanbanReasonTitle(string $reasonType): string
    {
        return match ($reasonType) {
            'completion' => 'Descreva o motivo da conclusÃ£o',
            'cancellation' => 'Descreva o motivo do cancelamento',
            'reopen' => 'Descreva o motivo da reabertura',
            default => 'Descreva o motivo',
        };
    }

    private function resetKanbanReason(): void
    {
        $this->pendingKanbanTaskId = null;
        $this->pendingKanbanFromStatusId = null;
        $this->pendingKanbanToStatusId = null;
        $this->pendingKanbanSourceOrder = [];
        $this->pendingKanbanTargetOrder = [];
        $this->pendingKanbanReasonType = null;
        $this->kanbanReason = '';
        $this->kanbanReasonTitle = '';
        $this->kanbanCompletionComment = '';
    }
}
