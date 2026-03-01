<?php

namespace App\Livewire\Task;

use App\Livewire\Traits\Modal;
use App\Livewire\Traits\WithFlashMessage;
use App\Models\Administration\Task\TaskCategory;
use App\Models\Administration\Task\TaskPriority;
use App\Models\Organization\OrganizationChart\OrganizationChart;
use App\Models\Task\Task;
use App\Models\Administration\User\User;
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

    public function boot(
        TaskService $taskService,
        TaskStatusService $taskStatusService,
        TaskStepStatusService $taskStepStatusService
    ): void
    {
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

    public function render()
    {
        return view('livewire.task.task-page', [
            'tasks' => $this->taskService->index($this->taskHubId, $this->filters),
            'dashboard' => $this->taskService->dashboard($this->taskHubId),
        ]);
    }
}
