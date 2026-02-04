<?php

namespace App\Livewire\Task;

use App\Livewire\Traits\Modal;
use App\Livewire\Traits\WithFlashMessage;
use App\Models\Administration\Task\TaskCategory;
use App\Models\Administration\Task\TaskPriority;
use App\Models\Administration\Task\TaskStepStatus;
use App\Models\Administration\User\User;
use App\Models\Organization\Workflow\Workflow;
use App\Models\Task\Task;
use App\Models\Task\TaskStep;
use App\Services\Administration\Task\TaskStatusService;
use App\Services\Administration\Task\TaskStepStatusService;
use App\Services\Task\TaskService;
use App\Validation\Task\TaskRules;
use App\Validation\Task\TaskStepRules;
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
        'perPage' => 50,
    ];

    public int $task_id;
    public int $workflow_id;

    public $title;
    public $description;
    public $user_id;
    public $task_category_id;
    public $task_priority_id;
    public $task_step_status_id;
    public $deadline_at;

    public $openCreateStep;
    public array $responsible = [];

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
            'workflows' => Workflow::orderBy('title')->get(),
        ])->layout('layouts.app');
    }
}
