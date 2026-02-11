<?php

namespace App\Livewire\Task;

use App\Livewire\Traits\WithFlashMessage;
use App\Models\Administration\Task\TaskCategory;
use App\Models\Administration\Task\TaskPriority;
use App\Models\Administration\Task\TaskStepCategory;
use App\Models\Administration\User\User;
use App\Models\Task\Task;
use App\Services\Administration\Task\TaskStatusService;
use App\Services\Administration\Task\TaskStepStatusService;
use App\Services\Task\TaskService;
use App\Validation\Task\TaskStepRules;
use Livewire\Component;

class TaskAside extends Component
{
    use WithFlashMessage;

    protected TaskService $taskService;
    protected TaskStatusService $taskStatusService;
    protected TaskStepStatusService $taskStepStatusesService;

    public $taskId;
    
    
    public $description = '';
    public $responsable_id;
    public $list_category_id;
    public $list_priority_id;
    public $list_status_id;
    public $deadline_at = null;

    public $isEditingDescription = false;
    public $savingDescription = false;    
    public $isEditingDeadline = false;
    public $savingDeadline = false;    
    
    public function boot( TaskService $taskService, TaskStatusService $taskStatusService, TaskStepStatusService $taskStepStatusesService )
    {
        $this->taskService = $taskService;
        $this->taskStatusService = $taskStatusService;
        $this->taskStepStatusesService = $taskStepStatusesService;
    }

    public function updatedResponsableId()
    {
        $data = $this->validate(TaskStepRules::responsable());

        Task::where('id', $this->taskId)->update([
            'user_id' => $data['responsable_id'],
        ]);
        
        $this->flashSuccess('Responsável atualizado.');
    }

    public function updatedListCategoryId()
    {
        $data = $this->validate(TaskStepRules::category());

        Task::where('id', $this->taskId)->update([
            'task_category_id' => $data['list_category_id'],
        ]);
        
        $this->flashSuccess('Categoria atualizada.');
    }

    public function updatedListPriorityId()
    {
        $data = $this->validate(TaskStepRules::priority());

        Task::where('id', $this->taskId)->update([
            'task_priority_id' => $data['list_priority_id'],
        ]);
        
        $this->flashSuccess('Prioridade atualizada.');
    }

    public function updatedListStatusId()
    {
        $data = $this->validate(TaskStepRules::status());

        Task::where('id', $this->taskId)->update([
            'task_status_id' => $data['list_status_id'],
        ]);
        
        $this->flashSuccess('Status atualizado.');
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
         $this->validate(TaskStepRules::description());
        
        $this->savingDescription = true;

        Task::findOrFail($this->taskId)->update([
            'description' => $this->description,
            'updated_at' => now(),
        ]);

        $this->isEditingDescription = false;
        $this->savingDescription = false;
        
        $this->flashSuccess('Descrição atualizada.');
    }    

    public function enableDeadlineEdit()
    {
        $this->isEditingDeadline = true;
        $this->deadline_at = Task::findOrFail($this->taskId)->deadline_at;
    }
    
    public function cancelDeadlineEdit()
    {
        $this->isEditingDeadline = false;
        $this->deadline_at = Task::findOrFail($this->taskId)->deadline_at;
        $this->savingDeadline = false;
    }
    
    public function saveDeadline()
    {
        $data = $this->validate(TaskStepRules::deadlineAt($this->taskId));
        
        $this->savingDeadline = true;
        
        Task::findOrFail($this->taskId)->update([
            'deadline_at' => $data['deadline_at'],
            'updated_at' => now(),
        ]);

        $this->isEditingDeadline = false;
        $this->savingDeadline = false;
        
        $this->flashSuccess('Prazo atualizado.');
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
