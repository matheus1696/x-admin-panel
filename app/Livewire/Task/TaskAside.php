<?php

namespace App\Livewire\Task;

use App\Models\Administration\Task\TaskCategory;
use App\Models\Administration\Task\TaskPriority;
use App\Models\Administration\Task\TaskStepCategory;
use App\Models\Administration\User\User;
use App\Models\Task\Task;
use App\Services\Administration\Task\TaskStatusService;
use App\Services\Administration\Task\TaskStepStatusService;
use App\Services\Task\TaskService;
use Livewire\Component;

class TaskAside extends Component
{
    protected TaskService $taskService;
    protected TaskStatusService $taskStatusService;
    protected TaskStepStatusService $taskStepStatusesService;

    public $taskId;
    public $isEditingDescription = false;
    public $description = '';
    public $savingDescription = false;    
    
    public function boot( TaskService $taskService, TaskStatusService $taskStatusService, TaskStepStatusService $taskStepStatusesService )
    {
        $this->taskService = $taskService;
        $this->taskStatusService = $taskStatusService;
        $this->taskStepStatusesService = $taskStepStatusesService;
    }

    public function enableDescriptionEdit()
    {
        $this->isEditingDescription = true;
        $this->description = Task::findOrFail($this->taskId)->description;
    }
    
    public function cancelDescriptionEdit()
    {
        $this->isEditingDescription = false;
        $this->description = Task::findOrFail($this->taskId)->description;
        $this->savingDescription = false;
    }
    
    public function saveDescription()
    {
        $this->validate([
            'description' => 'nullable|string|max:1000',
        ]);
        
        $this->savingDescription = true;

        Task::findOrFail($this->taskId)->update([
            'description' => $this->description,
            'updated_at' => now(),
        ]);

        $this->isEditingDescription = false;
        $this->savingDescription = false;
    }

    public function render()
    {
        return view('livewire.task.task-aside',[
            'task' => Task::find($this->taskId),
            'users' => User::orderBy('name')->get(),
            'taskStatuses' => $this->taskStatusService->index(),
            'taskCategories' => TaskCategory::orderBy('title')->get(),
            'taskPriorities' => TaskPriority::orderBy('level')->get(),
            'taskStepCategories' => TaskStepCategory::orderBy('title')->get(),
            'taskStepStatuses' => $this->taskStepStatusesService->index(),
        ]);
    }
}
