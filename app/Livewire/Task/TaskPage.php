<?php

namespace App\Livewire\Task;

use App\Livewire\Traits\Modal;
use App\Livewire\Traits\WithFlashMessage;
use App\Models\Administration\Task\TaskCategory;
use App\Models\Administration\Task\TaskPriority;
use App\Models\Administration\User\User;
use App\Services\Administration\Task\TaskStatusService;
use App\Services\Administration\Task\TaskStepStatusService;
use App\Services\Task\TaskService;
use App\Validation\Task\TaskRules;
use Livewire\Component;
use Livewire\WithPagination;

class TaskPage extends Component
{
    use Modal, WithFlashMessage, WithPagination;

    protected TaskService $taskService;
    protected TaskStatusService $taskStatusService;
    protected TaskStepStatusService $taskStepStatusesService;

    public array $filters = [
        'title' => '',
        'workflow_run_status_id' => 'all',
        'perPage' => 10,
    ];

    public array $newStep = [
        'title' => null,
        'deadline_at' => null,
        'user_id' => null,
    ];

    public $title;
    public $description;

    public function boot(
        TaskService $taskService, 
        TaskStatusService $taskStatusService, 
        TaskStepStatusService $taskStepStatusesService)
    {
        $this->taskService = $taskService;
        $this->taskStatusService = $taskStatusService;
        $this->taskStepStatusesService = $taskStepStatusesService;
    }

    public function updatedFilters()
    {
        $this->resetPage();
    }
    
    public function create()
    {
        $this->reset();
        $this->openModal('modal-form-create-task');
    }    
    
    public function createStep(int $id)
    {
        $this->reset();        
        $this->openModal('modal-form-create-task-step');
    }

    public function store()
    {
        $data = $this->validate(TaskRules::store());

        $this->taskService->create($data);

        $this->flashSuccess('Tarefa criada com sucesso.');
        $this->closeModal();
    }

    public function render()
    { 
        $tasks = $this->taskService->index($this->filters);
        $taskStatuses = $this->taskStatusService->index();
        $taskStepStatuses = $this->taskStepStatusesService->index();


        return view('livewire.task.task-page',[
            'tasks' => $tasks,
            'taskPriorities' => TaskPriority::orderBy('level')->get(),
            'taskCategories' => TaskCategory::orderBy('title')->get(),
            'taskStatuses' => $taskStatuses,
            'taskStepStatuses' => $taskStepStatuses,
            'users' => User::all(),
        ])->layout('layouts.app');
    }
}
