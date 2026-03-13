<?php

namespace App\Livewire\Task;

use App\Livewire\Traits\WithFlashMessage;
use App\Models\Administration\Task\TaskPriority;
use App\Services\Administration\Task\TaskStatusService;
use App\Services\Task\TaskCategoryService;
use App\Services\Task\TaskService;
use App\Support\Notifications\InteractsWithSystemNotifications;
use App\Validation\Task\TaskStepRules;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class TaskAside extends Component
{
    use InteractsWithSystemNotifications;
    use WithFlashMessage;

    protected TaskService $taskService;

    protected TaskCategoryService $taskCategoryService;

    protected TaskStatusService $taskStatusService;

    public $taskId;

    public $task;

    public Collection $users;

    public Collection $taskStatuses;

    public Collection $taskCategories;

    public Collection $taskPriorities;

    public $description = '';

    public $responsable_id;

    public $list_category_id;

    public $list_priority_id;

    public $list_status_id;

    public $deadline_at = null;

    public $comment;

    public $isEditingDescription = false;

    public $savingDescription = false;

    public $isEditingDeadline = false;

    public $savingDeadline = false;

    public $isLoading = true;

    public bool $showStatusReasonModal = false;

    public ?int $pendingTaskStatusToId = null;

    public ?string $pendingTaskStatusReasonType = null;

    public string $taskStatusTransitionReason = '';

    public function boot(
        TaskService $taskService,
        TaskCategoryService $taskCategoryService,
        TaskStatusService $taskStatusService
    ) {
        $this->taskService = $taskService;
        $this->taskCategoryService = $taskCategoryService;
        $this->taskStatusService = $taskStatusService;
    }

    public function mount($taskId)
    {
        $this->isLoading = true;
        $this->task = null;
        $this->taskId = $taskId;

        $this->users = collect();
        $this->taskPriorities = TaskPriority::orderBy('level')->get();
        $this->taskCategories = collect();
        $this->taskStatuses = collect();
        $this->loadTask();
    }

    public function loadTask()
    {
        $this->task = $this->taskService->find($this->taskId);
        $this->users = $this->taskService->accessUsersByHubId($this->task->task_hub_id);
        $this->taskCategories = $this->taskCategoryService->visibleForHub($this->task->task_hub_id, true);
        $this->taskStatuses = $this->taskStatusService->index($this->task->task_hub_id);
        $this->list_status_id = $this->task->task_status_id;
        $this->isLoading = false;
    }

    public function updatedResponsableId()
    {
        $allowedUserIds = $this->users
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();
        $previousResponsibleId = (int) ($this->task->user_id ?? 0);

        $data = $this->validate(TaskStepRules::responsable($allowedUserIds));

        $this->task = $this->taskService->updateTaskResponsible($this->task->id, $data['responsable_id']);

        if ($this->task->user && (int) $this->task->user->id !== $previousResponsibleId) {
            $this->notifyUsers(
                $this->task->user,
                'Voce foi associado a uma tarefa',
                'A tarefa '.$this->task->code.' - '.$this->task->title.' foi atribuida a voce.',
                [
                    'url' => route('tasks.show', $this->task->taskHub->uuid),
                    'icon' => 'fa-solid fa-list-check',
                    'level' => 'info',
                ]
            );
        }

        $this->flashSuccess('Responsavel atualizado.');
    }

    public function updatedListCategoryId()
    {
        $availableCategoryIds = $this->taskCategories
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();

        $data = $this->validate([
            'list_category_id' => ['nullable', Rule::in($availableCategoryIds)],
        ]);

        $this->task = $this->taskService->updateTaskCategory($this->task->id, $data['list_category_id']);
        $this->flashSuccess('Categoria atualizada.');
    }

    public function updatedListPriorityId()
    {
        $data = $this->validate(TaskStepRules::priority());

        $this->task = $this->taskService->updateTaskPriority($this->task->id, $data['list_priority_id']);
        $this->flashSuccess('Prioridade atualizada.');
    }

    public function updatedListStatusId()
    {
        $availableStatusIds = $this->taskStatuses
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();

        $data = $this->validate([
            'list_status_id' => ['nullable', Rule::in($availableStatusIds)],
        ]);

        $fromStatusId = (int) ($this->task->task_status_id ?? 0);
        $toStatusId = (int) ($data['list_status_id'] ?? 0);

        if ($this->taskService->isInvalidTaskTerminalSwap(
            $this->task->task_hub_id,
            $fromStatusId === 0 ? null : $fromStatusId,
            $toStatusId === 0 ? null : $toStatusId
        )) {
            $this->list_status_id = $this->task->task_status_id;
            $this->flashError('Não é permitido mover uma tarefa concluída para cancelada ou cancelada para concluída.');

            return;
        }

        $reasonType = $this->taskService->taskReasonTypeForTransition(
            $this->task->task_hub_id,
            $fromStatusId === 0 ? null : $fromStatusId,
            $toStatusId === 0 ? null : $toStatusId
        );

        if ($fromStatusId !== $toStatusId && $reasonType !== null) {
            $this->pendingTaskStatusToId = $toStatusId;
            $this->pendingTaskStatusReasonType = $reasonType;
            $this->taskStatusTransitionReason = '';
            $this->showStatusReasonModal = true;
            $this->list_status_id = $this->task->task_status_id;

            return;
        }

        $this->applyTaskStatusChange($toStatusId);
    }

    public function confirmTaskStatusTransitionReason(): void
    {
        $this->validate([
            'taskStatusTransitionReason' => ['required', 'string', 'max:2000'],
        ]);

        if ($this->pendingTaskStatusToId === null) {
            $this->cancelTaskStatusTransitionReason();

            return;
        }

        $this->applyTaskStatusChange(
            $this->pendingTaskStatusToId,
            trim($this->taskStatusTransitionReason),
            $this->pendingTaskStatusReasonType
        );

        $this->resetPendingTaskStatusTransition();
    }

    public function cancelTaskStatusTransitionReason(): void
    {
        $this->resetPendingTaskStatusTransition();
        $this->list_status_id = $this->task->task_status_id;
    }

    private function applyTaskStatusChange(int $toStatusId, ?string $reason = null, ?string $reasonType = null): void
    {
        $fromStatusId = (int) ($this->task->task_status_id ?? 0);
        $columns = collect($this->taskService->kanban($this->task->taskHub->uuid));
        $sourceColumn = $columns->firstWhere('status_id', $fromStatusId);
        $targetColumn = $columns->firstWhere('status_id', $toStatusId);

        $sourceOrder = collect($sourceColumn['tasks'] ?? [])
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->reject(fn (int $id): bool => $id === (int) $this->task->id)
            ->values()
            ->all();

        $targetOrder = collect($targetColumn['tasks'] ?? [])
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->reject(fn (int $id): bool => $id === (int) $this->task->id)
            ->push((int) $this->task->id)
            ->unique()
            ->values()
            ->all();

        $moved = $this->taskService->moveKanbanTask(
            $this->task->taskHub->uuid,
            (int) $this->task->id,
            $fromStatusId,
            $toStatusId,
            $sourceOrder,
            $targetOrder,
            $reason,
            $reasonType
        );

        if (! $moved) {
            $this->loadTask();
            $this->list_status_id = $this->task->task_status_id;
            $this->flashError('A tarefa só pode ser concluída quando todas as etapas estiverem concluídas.');

            return;
        }

        $this->loadTask();
        $this->flashSuccess('Status atualizado.');
    }

    private function resetPendingTaskStatusTransition(): void
    {
        $this->showStatusReasonModal = false;
        $this->pendingTaskStatusToId = null;
        $this->pendingTaskStatusReasonType = null;
        $this->taskStatusTransitionReason = '';
        $this->resetValidation();
    }

    public function enableDescriptionEdit()
    {
        $this->isEditingDescription = true;
        $this->description = $this->task->description;
    }

    public function cancelDescriptionEdit()
    {
        $this->isEditingDescription = false;
        $this->description = $this->task->description;
        $this->savingDescription = false;
    }

    public function saveDescription()
    {
        $data = $this->validate(TaskStepRules::description());

        $this->savingDescription = true;

        $this->task = $this->taskService->updateTaskDescription($this->task->id, $data['description'] ?? null);

        $this->isEditingDescription = false;
        $this->savingDescription = false;

        $this->flashSuccess('Descricao atualizada.');
    }

    public function enableDeadlineEdit()
    {
        $this->isEditingDeadline = true;
        $this->deadline_at = $this->task->deadline_at;
    }

    public function cancelDeadlineEdit()
    {
        $this->isEditingDeadline = false;
        $this->deadline_at = $this->task->deadline_at;
        $this->savingDeadline = false;
    }

    public function saveDeadline()
    {
        $data = $this->validate(TaskStepRules::deadlineAt());

        $this->savingDeadline = true;

        $this->task = $this->taskService->updateTaskDeadline($this->task->id, $data['deadline_at']);

        $this->isEditingDeadline = false;
        $this->savingDeadline = false;

        $this->flashSuccess('Prazo atualizado.');
    }

    public function storeComment()
    {
        $data = $this->validate(TaskStepRules::storeComment());

        $this->taskService->storeComment($this->taskId, $data, 'comment');

        $this->comment = '';
        $this->task->refresh();
    }

    public function taskFinished()
    {
        $previousStatusId = (int) ($this->task->task_status_id ?? 0);

        $this->taskService->changeStatus(
            $this->task->id,
            4,
            Auth::user()->name.' marcou a tarefa como concluida',
            'finished_change'
        );

        $this->task->refresh();

        if ((int) ($this->task->task_status_id ?? 0) === $previousStatusId) {
            $this->flashError('A tarefa só pode ser concluída quando todas as etapas estiverem concluídas.');

            return;
        }

        $this->flashSuccess('Tarefa marcada como concluida.');
    }

    public function render()
    {
        return view('livewire.task.task-aside');
    }
}
