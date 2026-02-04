<?php

namespace App\Livewire\Task;

use App\Livewire\Traits\Modal;
use App\Livewire\Traits\WithFlashMessage;
use App\Models\Administration\Task\TaskCategory;
use App\Models\Administration\Task\TaskPriority;
use App\Models\Administration\Task\TaskStepStatus;
use App\Models\Administration\User\User;
use App\Services\Administration\Task\TaskStatusService;
use App\Services\Task\TaskService;
use App\Validation\Task\TaskRules;
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

    public $title;
    public $user_id;
    public $task_category_id;
    public $task_priority_id;
    public $task_status_id;
    public $deadline_at;

    public function boot( TaskService $taskService,  TaskStatusService $taskStatusService)
    {
        $this->taskService = $taskService;
        $this->taskStatusService = $taskStatusService;
    }

    protected function setDefaults(): void
    {
        $this->task_priority_id = TaskPriority::where('is_default', true)->value('id');
        $this->task_status_id = TaskStepStatus::where('is_default', true)->value('id');
    }

    public function mount()
    {
        $this->setDefaults();
    }

    public function resetForm()
    {
        $this->reset('title', 'user_id', 'task_category_id', 'task_priority_id', 'task_status_id', 'deadline_at');
    }
    
    public function create()
    {
        $this->reset();
        $this->openModal('modal-form-create-task');
    }

    public function store()
    {
        $data = $this->validate(TaskRules::store());

        if ($data['task_status_id'] == 2) {            
            $data['started_at'] = now();
        }

        $this->taskService->create($data);

        $this->resetForm();
        $this->flashSuccess('Tarefa criada com sucesso.');
        $this->setDefaults();
    }

    public function render()
    { 
        return view('livewire.task.task-page',[
            'tasks' => $this->taskService->index($this->filters),
            'users' => User::orderBy('name')->get(),
            'taskCategories' => TaskCategory::orderBy('title')->get(),
            'taskPriorities' => TaskPriority::orderBy('level')->get(),
            'taskStatuses' => $this->taskStatusService->index(),
        ]);
    }
}
