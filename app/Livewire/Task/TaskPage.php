<?php

namespace App\Livewire\Task;

use App\Livewire\Traits\Modal;
use App\Livewire\Traits\WithFlashMessage;
use App\Models\Administration\Task\TaskCategory;
use App\Models\Administration\Task\TaskPriority;
use App\Models\Administration\Task\TaskStepStatus;
use App\Models\Administration\User\User;
use App\Models\Organization\OrganizationChart\OrganizationChart;
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

    protected TaskStepStatusService $taskStepStatusService;

    public array $filters = [
        'title' => '',
        'workflow_run_status_id' => 'all',
        'perPage' => 50,
    ];

    public Collection $users;

    public Collection $taskCategories;

    public Collection $taskPriorities;

    public Collection $taskStatuses;

    public Collection $organizations;

    public Collection $taskStepStatuses;

    public string $taskHubId;

    public int $taskHubInternalId;

    public int $taskHubOwnerId;

    public ?int $selectedTaskId = null;

    public ?int $selectedStepId = null;

    public ?string $title = null;

    public ?int $user_id = null;

    public ?int $task_category_id = null;

    public ?int $task_priority_id = null;

    public ?int $task_status_id = null;

    public ?string $step_title = null;

    public ?int $step_user_id = null;

    public ?int $organization_id = null;

    public ?int $step_task_priority_id = null;

    public ?int $task_step_status_id = null;

    public ?int $member_user_id = null;

    public ?int $member_organization_id = null;

    public string $sharingMode = 'user';

    public ?int $pendingStepMoveStepId = null;

    public ?int $pendingStepMoveFromStatusId = null;

    public ?int $pendingStepMoveToStatusId = null;

    /**
     * @var array<int>
     */
    public array $pendingStepMoveTargetOrder = [];

    public string $stepCompletionComment = '';

    public ?string $pendingStepMoveReasonType = null;

    public function boot(
        TaskService $taskService,
        TaskStatusService $taskStatusService,
        TaskStepStatusService $taskStepStatusService
    ): void {
        $this->taskService = $taskService;
        $this->taskStatusService = $taskStatusService;
        $this->taskStepStatusService = $taskStepStatusService;
    }

    protected function setDefaults(): void
    {
        $this->task_priority_id = $this->taskPriorities->firstWhere('is_default', true)?->id;
        $this->task_status_id = $this->taskStatuses->firstWhere('is_default', true)?->id;
    }

    protected function setStepDefaults(): void
    {
        $this->step_task_priority_id = $this->taskPriorities->firstWhere('is_default', true)?->id;
        $this->task_step_status_id = $this->taskStepStatuses->firstWhere('is_default', true)?->id;
    }

    public function mount(string $uuid): void
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
        $this->taskHubInternalId = $taskHub->id;
        $this->taskHubOwnerId = (int) $taskHub->owner_id;
        $this->users = User::orderBy('name')->get();
        $this->organizations = OrganizationChart::orderBy('order')->get();
        $this->taskCategories = TaskCategory::orderBy('title')->get();
        $this->taskPriorities = TaskPriority::orderBy('level')->get();
        $this->taskStatuses = $this->taskStatusService->index();
        $this->taskStepStatuses = $this->taskStepStatusService->index();
        $this->setStepDefaults();
    }

    public function updatedFilters(): void
    {
        $this->resetPage();
    }

    public function resetForm(): void
    {
        $this->reset('title', 'user_id', 'task_category_id', 'task_priority_id', 'task_status_id');
    }

    public function enableCreateTask(): void
    {
        $this->resetForm();
        $this->setDefaults();
        $this->openModal('modal-task-create');
    }

    public function cancelCreateTask(): void
    {
        $this->resetForm();
        $this->setDefaults();
        $this->closeModal();
    }

    public function resetStepForm(): void
    {
        $this->reset('step_title', 'step_user_id', 'organization_id', 'step_task_priority_id', 'task_step_status_id');
    }

    public function cancelCreateTaskStep(): void
    {
        $this->resetStepForm();
        $this->setStepDefaults();
    }

    public function storeTask(): void
    {
        $data = $this->validate(TaskRules::store());

        $this->taskService->create($this->taskHubId, $data);

        $this->resetForm();
        $this->closeModal();
        $this->flashSuccess('Tarefa criada com sucesso.');
    }

    public function openAsideTask(int $id): void
    {
        $this->selectedStepId = null;
        $this->selectedTaskId = $id;
    }

    public function openAsideTaskStep(int $id): void
    {
        $this->selectedTaskId = null;
        $this->selectedStepId = TaskStep::query()
            ->where('task_hub_id', $this->taskHubInternalId)
            ->findOrFail($id)
            ->id;
    }

    public function storeTaskStep(int $taskId): void
    {
        $data = $this->validate([
            'step_title' => TaskStepRules::store()['title'],
            'step_user_id' => TaskStepRules::store()['user_id'],
            'organization_id' => TaskStepRules::store()['organization_id'],
            'step_task_priority_id' => TaskStepRules::store()['task_priority_id'],
            'task_step_status_id' => TaskStepRules::store()['task_step_status_id'],
        ]);

        $task = Task::query()
            ->where('task_hub_id', $this->taskHubInternalId)
            ->findOrFail($taskId);

        TaskStep::create([
            'task_hub_id' => $this->taskHubInternalId,
            'task_id' => $task->id,
            'title' => $data['step_title'],
            'user_id' => $data['step_user_id'],
            'organization_id' => $data['organization_id'],
            'task_priority_id' => $data['step_task_priority_id'],
            'task_status_id' => $data['task_step_status_id'],
            'kanban_order' => $this->taskService->nextStepKanbanOrder($this->taskHubInternalId, $data['task_step_status_id']),
            'created_user_id' => Auth::id(),
        ]);

        $this->cancelCreateTaskStep();
        $this->dispatch('step-form-closed');
        $this->flashSuccess('Etapa criada com sucesso.');
    }

    public function closedAsideTask(): void
    {
        $this->selectedTaskId = null;
    }

    public function closedAsideTaskStep(): void
    {
        $this->selectedStepId = null;
    }

    public function addMember(): void
    {
        $data = $this->validate([
            'member_user_id' => 'required|exists:users,id',
        ]);

        $added = $this->taskService->addMember(
            $this->taskHubId,
            (int) Auth::id(),
            (int) $data['member_user_id']
        );

        if (! $added) {
            $this->flashError('Apenas o proprietário pode gerenciar os membros do ambiente.');

            return;
        }

        $this->member_user_id = null;
        $this->flashSuccess('Membro adicionado ao ambiente com sucesso.');
    }

    public function addMembersByOrganization(): void
    {
        $data = $this->validate([
            'member_organization_id' => 'required|exists:organization_charts,id',
        ]);

        $addedCount = $this->taskService->addMembersByOrganization(
            $this->taskHubId,
            (int) Auth::id(),
            (int) $data['member_organization_id']
        );

        if ($addedCount === 0) {
            $this->flashError('Nenhum usuário novo foi adicionado para este setor.');

            return;
        }

        $this->member_organization_id = null;
        $this->flashSuccess($addedCount.' usuário(s) adicionados ao ambiente.');
    }

    public function removeMember(int $membershipId): void
    {
        $removed = $this->taskService->removeMember(
            $this->taskHubId,
            (int) Auth::id(),
            $membershipId
        );

        if (! $removed) {
            $this->flashError('Não foi possível remover este membro do ambiente.');

            return;
        }

        $this->flashSuccess('Membro removido do ambiente com sucesso.');
    }

    public function requestStepKanbanDrop(int $stepId, int $fromStatusId, int $toStatusId, array $targetOrder): void
    {
        if ($this->isInvalidStepTerminalSwap($fromStatusId, $toStatusId)) {
            $this->flashError('Não é permitido mover uma etapa cancelada para concluída ou uma etapa concluída para cancelada.');

            return;
        }

        $reasonType = $this->stepReasonTypeForTransition($fromStatusId, $toStatusId);

        if (
            $fromStatusId !== $toStatusId
            && $reasonType !== null
        ) {
            $this->pendingStepMoveStepId = $stepId;
            $this->pendingStepMoveFromStatusId = $fromStatusId;
            $this->pendingStepMoveToStatusId = $toStatusId;
            $this->pendingStepMoveTargetOrder = array_map(fn ($id): int => (int) $id, $targetOrder);
            $this->stepCompletionComment = '';
            $this->pendingStepMoveReasonType = $reasonType;
            $this->openModal('modal-step-completion-move');

            return;
        }

        $this->performStepKanbanDrop($stepId, $fromStatusId, $toStatusId, $targetOrder);
    }

    public function reorderStepKanbanCard(
        int $stepId,
        int $fromStatusId,
        int $toStatusId,
        array $targetOrder,
        ?string $completionComment = null
    ): void {
        $completionComment = $completionComment !== null ? trim($completionComment) : null;

        if ($this->isInvalidStepTerminalSwap($fromStatusId, $toStatusId)) {
            $this->flashError('Não é permitido mover uma etapa cancelada para concluída ou uma etapa concluída para cancelada.');

            return;
        }

        $reasonType = $this->stepReasonTypeForTransition($fromStatusId, $toStatusId);

        if (
            $fromStatusId !== $toStatusId
            && $reasonType !== null
            && ($completionComment === null || $completionComment === '')
        ) {
            $this->flashError(
                match ($reasonType) {
                    'completion' => 'Informe um comentário para concluir a etapa.',
                    'cancellation' => 'Informe o motivo para cancelar a etapa.',
                    'reopen' => 'Informe o motivo para reabrir a etapa.',
                    default => 'Informe um motivo para continuar.',
                }
            );

            return;
        }

        $columns = collect($this->taskService->stepKanban($this->taskHubId));

        $sourceColumn = $columns->firstWhere('status_id', $fromStatusId);
        $targetColumn = $columns->firstWhere('status_id', $toStatusId);

        if (! $sourceColumn || ! $targetColumn) {
            return;
        }

        $sourceOrder = $sourceColumn['steps']
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->reject(fn (int $id): bool => $id === $stepId)
            ->values()
            ->all();

        $targetIds = $targetColumn['steps']
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->reject(fn (int $id): bool => $id === $stepId)
            ->values()
            ->all();

        $allowedIds = [...$targetIds, $stepId];

        $normalizedTargetOrder = collect($targetOrder)
            ->map(fn ($id): int => (int) $id)
            ->filter(fn (int $id): bool => in_array($id, $allowedIds, true))
            ->unique()
            ->values();

        foreach ($allowedIds as $allowedId) {
            if (! $normalizedTargetOrder->contains($allowedId)) {
                $normalizedTargetOrder->push($allowedId);
            }
        }

        $targetOrder = $normalizedTargetOrder->all();

        $this->taskService->moveKanbanStep(
            $this->taskHubId,
            $stepId,
            $fromStatusId,
            $toStatusId,
            $sourceOrder,
            $targetOrder,
            $completionComment,
            $completionComment !== null && $fromStatusId !== $toStatusId ? $reasonType : null
        );
    }

    public function confirmStepCompletionMove(): void
    {
        $this->validate([
            'stepCompletionComment' => 'required|string|max:2000',
        ]);

        if (
            $this->pendingStepMoveStepId === null
            || $this->pendingStepMoveFromStatusId === null
            || $this->pendingStepMoveToStatusId === null
        ) {
            $this->closeModal();

            return;
        }

        $this->performStepKanbanDrop(
            $this->pendingStepMoveStepId,
            $this->pendingStepMoveFromStatusId,
            $this->pendingStepMoveToStatusId,
            $this->pendingStepMoveTargetOrder,
            $this->stepCompletionComment
        );

        $this->closeModal();
    }

    public function closeModal(): void
    {
        $this->modalKey = null;
        $this->showModal = false;
        $this->resetValidation();
        $this->resetPendingStepMove();
    }

    /**
     * @return array<int>
     */
    private function stepCompletionStatusIds(): array
    {
        return TaskStepStatus::query()
            ->where('title', 'Concluída')
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();
    }

    /**
     * @return array<int>
     */
    private function stepCancellationStatusIds(): array
    {
        return TaskStepStatus::query()
            ->where('title', 'Cancelada')
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();
    }

    /**
     * @return array<int>
     */
    private function stepTerminalStatusIds(): array
    {
        return TaskStepStatus::query()
            ->whereIn('title', ['Concluída', 'Cancelada'])
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();
    }

    private function stepReasonTypeForTransition(int $fromStatusId, int $toStatusId): ?string
    {
        if ($fromStatusId === $toStatusId) {
            return null;
        }

        if (in_array($toStatusId, $this->stepCompletionStatusIds(), true)) {
            return 'completion';
        }

        if (in_array($toStatusId, $this->stepCancellationStatusIds(), true)) {
            return 'cancellation';
        }

        if (
            in_array($fromStatusId, $this->stepTerminalStatusIds(), true)
            && ! in_array($toStatusId, $this->stepTerminalStatusIds(), true)
        ) {
            return 'reopen';
        }

        return null;
    }

    private function isInvalidStepTerminalSwap(int $fromStatusId, int $toStatusId): bool
    {
        if ($fromStatusId === $toStatusId) {
            return false;
        }

        return in_array($fromStatusId, $this->stepTerminalStatusIds(), true)
            && in_array($toStatusId, $this->stepTerminalStatusIds(), true);
    }

    private function performStepKanbanDrop(
        int $stepId,
        int $fromStatusId,
        int $toStatusId,
        array $targetOrder,
        ?string $completionComment = null
    ): void {
        $this->reorderStepKanbanCard($stepId, $fromStatusId, $toStatusId, $targetOrder, $completionComment);
    }

    private function resetPendingStepMove(): void
    {
        $this->pendingStepMoveStepId = null;
        $this->pendingStepMoveFromStatusId = null;
        $this->pendingStepMoveToStatusId = null;
        $this->pendingStepMoveTargetOrder = [];
        $this->stepCompletionComment = '';
        $this->pendingStepMoveReasonType = null;
    }

    public function render()
    {
        $members = $this->taskService->members($this->taskHubId);
        $memberUserIds = $members
            ->pluck('user_id')
            ->map(fn ($id): int => (int) $id)
            ->all();

        return view('livewire.task.task-page', [
            'tasks' => $this->taskService->index($this->taskHubId, $this->filters),
            'dashboard' => $this->taskService->dashboard($this->taskHubId),
            'stepKanban' => $this->taskService->stepKanban($this->taskHubId),
            'stepCompletionStatusIds' => $this->stepCompletionStatusIds(),
            'members' => $members,
            'canManageMembers' => $this->taskHubOwnerId === (int) Auth::id(),
            'availableMemberUsers' => $this->users
                ->reject(fn (User $user): bool => in_array((int) $user->id, $memberUserIds, true))
                ->values(),
        ]);
    }
}
