<?php

namespace App\Livewire\Task;

use App\Livewire\Traits\WithFlashMessage;
use App\Models\Administration\Task\TaskCategory;
use App\Models\Administration\Task\TaskPriority;
use App\Models\Administration\Task\TaskStepCategory;
use App\Models\Administration\User\User;
use App\Models\Task\TaskActivity;
use App\Services\Administration\Task\TaskStatusService;
use App\Services\Administration\Task\TaskStepStatusService;
use App\Services\Task\TaskService;
use App\Validation\Task\TaskStepRules;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TaskAside extends Component
{
    use WithFlashMessage;

    protected TaskService $taskService;
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
    
    public function boot( TaskService $taskService, TaskStatusService $taskStatusService )
    {
        $this->taskService = $taskService;
        $this->taskStatusService = $taskStatusService;
    }

    public function mount($taskId)
    {
        $this->isLoading = true;
        $this->task = null;
        $this->taskId = $taskId;

        // Listas estáticas
        $this->users = User::orderBy('name')->get();
        $this->taskPriorities = TaskPriority::orderBy('level')->get();
        $this->taskCategories = TaskCategory::orderBy('title')->get();
        $this->taskStatuses = $this->taskStatusService->index();
    }

    public function loadTask()
    {
        $this->task = $this->taskService->find($this->taskId);
        $this->isLoading = false;
    }

    public function updatedResponsableId()
    {
        $data = $this->validate(TaskStepRules::responsable());

        $this->task->update([
            'user_id' => $data['responsable_id'],
            'updated_at' => now(),
        ]);

        TaskActivity::create([
            'task_id'     => $this->task->id,
            'user_id'     => Auth::user()->id,
            'type'        => 'responsable_change',
            'description' => Auth::user()->name.' alterou o responsável',
        ]);
        
        $this->task->refresh();
        $this->flashSuccess('Responsável atualizado.');
    }

    public function updatedListCategoryId()
    {
        $data = $this->validate(TaskStepRules::category());

        $this->task->update([
            'task_category_id' => $data['list_category_id'],
        ]);

        TaskActivity::create([
            'task_id'     => $this->task->id,
            'user_id'     => Auth::user()->id,
            'type'        => 'category_change',
            'description' => Auth::user()->name.' alterou a categoria',
        ]);
        
        $this->task->refresh();
        $this->flashSuccess('Categoria atualizada.');
    }

    public function updatedListPriorityId()
    {
        $data = $this->validate(TaskStepRules::priority());

        $this->task->update([
            'task_priority_id' => $data['list_priority_id'],
        ]);

        TaskActivity::create([
            'task_id'     => $this->task->id,
            'user_id'     => Auth::user()->id,
            'type'        => 'priority_change',
            'description' => Auth::user()->name.' alterou a prioridade',
        ]);
        
        $this->task->refresh();
        $this->flashSuccess('Prioridade atualizada.');
    }

    public function updatedListStatusId()
    {
        $data = $this->validate(TaskStepRules::status());

        if ($data['list_status_id'] == 2) {
            $this->task->update([
                'task_status_id' => $data['list_status_id'],
                'started_at' => now(),
            ]);
        } else {
            $this->task->update([
                'task_status_id' => $data['list_status_id'],
            ]);
        }
        

        

        TaskActivity::create([
            'task_id'     => $this->task->id,
            'user_id'     => Auth::user()->id,
            'type'        => 'status_change',
            'description' => Auth::user()->name.' alterou o status',
        ]);
        
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

        $this->task->update($data);

        TaskActivity::create([
            'task_id'     => $this->task->id,
            'user_id'     => Auth::user()->id,
            'type'        => 'description_change',
            'description' => Auth::user()->name.' alterou a descrição',
        ]);

        $this->isEditingDescription = false;
        $this->savingDescription = false;
        
        $this->task->refresh();
        $this->flashSuccess('Descrição atualizada.');
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
        $data = $this->validate(TaskStepRules::deadlineAt($this->taskId));
        
        $this->savingDeadline = true;
        
        $this->task->update([
            'deadline_at' => $data['deadline_at'],
            'updated_at' => now(),
        ]);

        TaskActivity::create([
            'task_id'     => $this->task->id,
            'user_id'     => Auth::user()->id,
            'type'        => 'deadline_change',
            'description' => Auth::user()->name.' alterou o prazo',
        ]);

        $this->isEditingDeadline = false;
        $this->savingDeadline = false;
        
        $this->task->refresh();
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
        $this->task->update([
            'task_status_id' => 4,
            'finished_at' => now(),
        ]); 

        TaskActivity::create([
            'task_id'     => $this->task->id,
            'user_id'     => Auth::user()->id,
            'type'        => 'finished_change',
            'description' => Auth::user()->name.' marcou a tarefa como concluída',
        ]);

        $this->flashSuccess('Tarefa marcada como concluída.');
        $this->task->refresh();
    }

    public function render()
    {
        return view('livewire.task.task-aside');
    }
}
