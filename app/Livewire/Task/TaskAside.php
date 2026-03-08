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

    public Collection $workflows;

    public $description = '';

    public $responsable_id;

    public $list_category_id;

    public $list_priority_id;

    public $list_status_id;

    public $deadline_at = null;

    public $comment;

    public $workflow_id = null;

    public $isEditingDescription = false;

    public $savingDescription = false;

    public $isEditingDeadline = false;

    public $savingDeadline = false;

    public $isLoading = true;

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
        $this->workflows = $this->taskService->availableWorkflows();
        $this->workflow_id = $this->workflows->first()?->id;

        $this->loadTask();
    }

    public function loadTask()
    {
        $this->task = $this->taskService->find($this->taskId);
        $this->users = $this->taskService->accessUsersByHubId($this->task->task_hub_id);
        $this->taskCategories = $this->taskCategoryService->visibleForHub($this->task->task_hub_id, true);
        $this->taskStatuses = $this->taskStatusService->index($this->task->task_hub_id);
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

        $this->taskService->changeStatus($this->task->id, $data['list_status_id']);

        $this->task->refresh();
        $this->flashSuccess('Status atualizado.');
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

    public function copyWorkflowToTask(): void
    {
        $workflowIds = $this->workflows
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();

        $data = $this->validate([
            'workflow_id' => ['required', Rule::in($workflowIds)],
        ]);

        $copied = $this->taskService->copyWorkflowToTask($this->task->id, (int) $data['workflow_id']);

        if (! $copied) {
            $this->loadTask();
            $this->flashError('Nao foi possivel copiar o fluxo. Esta tarefa ja possui etapas ou o fluxo selecionado esta invalido.');

            return;
        }

        $this->loadTask();
        $this->flashSuccess('Fluxo de trabalho copiado para a tarefa com sucesso.');
    }

    public function taskFinished()
    {
        $this->taskService->changeStatus(
            $this->task->id,
            4,
            Auth::user()->name.' marcou a tarefa como concluida',
            'finished_change'
        );

        $this->flashSuccess('Tarefa marcada como concluida.');
        $this->task->refresh();
    }

    public function render()
    {
        return view('livewire.task.task-aside');
    }
}
