<?php

namespace App\Livewire\Task;

use App\Livewire\Traits\Modal;
use App\Livewire\Traits\WithFlashMessage;
use App\Models\Administration\Task\TaskCategory;
use App\Models\Administration\Task\TaskPriority;
use App\Models\Administration\User\User;
use App\Services\Administration\Task\TaskStatusService;
use App\Services\Task\TaskService;
use App\Validation\Task\TaskRules;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class TaskPage extends Component
{
    use Modal, WithFlashMessage, WithPagination;

    protected TaskService $taskService;
    protected TaskStatusService $taskStatusService;

    public array $filters = [
        'title' => '',
        'workflow_run_status_id' => 'all',
        'perPage' => 50,
    ];

    public Collection $users;
    public Collection $taskCategories;
    public Collection $taskPriorities;
    public Collection $taskStatuses;

    public ?string $title = null;
    public ?int $user_id = null;
    public ?int $task_category_id = null;
    public ?int $task_priority_id = null;
    public ?int $task_status_id = null;

    public bool $isCreatingTask = false;

    public function boot( TaskService $taskService,  TaskStatusService $taskStatusService)
    {
        $this->taskService = $taskService;
        $this->taskStatusService = $taskStatusService;
    }

    protected function setDefaults(): void
    {
        $this->task_priority_id = $this->taskPriorities->firstWhere('is_default', true)?->id;
        $this->task_status_id = collect($this->taskStatuses)->firstWhere('is_default', true)?->id;
    }

    public function mount()
    {
        $this->users = User::orderBy('name')->get();
        $this->taskCategories = TaskCategory::orderBy('title')->get();
        $this->taskPriorities = TaskPriority::orderBy('level')->get();
        $this->taskStatuses = $this->taskStatusService->index();
    }

    public function updatedFilters()
    {
        $this->resetPage();
    }

    public function resetForm()
    {
        $this->reset('title', 'user_id', 'task_category_id', 'task_priority_id', 'task_status_id');
    }

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

    public function store()
    {
        $data = $this->validate(TaskRules::store());

        $this->taskService->create($data);

        $this->isCreatingTask = false;
        $this->resetForm();
        $this->flashSuccess('Tarefa criada com sucesso.');
    }

    public function render()
    { 
        return view('livewire.task.task-page',[
            'tasks' => $this->taskService->index($this->filters),
        ]);
    }
}
