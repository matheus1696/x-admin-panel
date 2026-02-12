<?php

namespace App\Livewire\Task;

use App\Livewire\Traits\WithFlashMessage;
use App\Models\Administration\Task\TaskCategory;
use App\Models\Administration\Task\TaskPriority;
use App\Models\Administration\Task\TaskStepCategory;
use App\Models\Administration\User\User;
use App\Models\Organization\OrganizationChart\OrganizationChart;
use App\Models\Task\Task;
use App\Models\Task\TaskActivity;
use App\Models\Task\TaskStep;
use App\Models\Task\TaskStepActivity;
use App\Services\Administration\Task\TaskStepStatusService;
use App\Validation\Task\TaskStepRules;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

use function Livewire\str;

class TaskStepAside extends Component
{
    use WithFlashMessage;
    
    protected TaskStepStatusService $taskStepStatusesService;

    public $stepId;
    public $step;

    public Collection $users;
    public Collection $organizations;
    public Collection $taskPriorities;
    public Collection $taskStepCategories;
    public Collection $taskStepStatuses;
        
    public $description = '';
    public $responsable_id;
    public $organization_responsable_id;
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

    public function boot( TaskStepStatusService $taskStepStatusesService )
    {
        $this->taskStepStatusesService = $taskStepStatusesService;
    }

    public function mount($stepId)
    {
        $this->isLoading = true;
        $this->step = null;
        $this->stepId = $stepId;

        // Listas estáticas
        $this->users = User::orderBy('name')->get();
        $this->organizations = OrganizationChart::orderBy('order')->get();
        $this->taskPriorities = TaskPriority::orderBy('level')->get();
        $this->taskStepCategories = TaskStepCategory::orderBy('title')->get();
        $this->taskStepStatuses = $this->taskStepStatusesService->index();
    }

    public function loadStep()
    {
        $this->step = TaskStep::with(['taskPriority','taskStepStatus','taskStepCategory'])->findOrFail($this->stepId);
        $this->isLoading = false;
    }    

    public function updatedOrganizationResponsableId()
    {
        $data = $this->validate(TaskStepRules::organizationResponsable());

        $this->step->update([
            'organization_id' => $data['organization_responsable_id'],
            'updated_at' => now(),
        ]);

        TaskStepActivity::create([
            'task_step_id'     => $this->step->id,
            'user_id'     => Auth::user()->id,
            'type'        => 'organization_responsable_change',
            'description' => Auth::user()->name.' alterou o responsável',
        ]);
        
        $this->flashSuccess('Responsável atualizado.');
        $this->step->refresh();
    }

    public function updatedResponsableId()
    {
        $data = $this->validate(TaskStepRules::responsable());

        $this->step->update([
            'user_id' => $data['responsable_id'],
            'updated_at' => now(),
        ]);

        TaskStepActivity::create([
            'task_step_id'     => $this->step->id,
            'user_id'     => Auth::user()->id,
            'type'        => 'responsable_change',
            'description' => Auth::user()->name.' alterou o responsável',
        ]);
        
        $this->flashSuccess('Responsável atualizado.');
        $this->step->refresh();
    }

    public function updatedListCategoryId()
    {
        $data = $this->validate(TaskStepRules::category());

        $this->step->update([
            'task_category_id' => $data['list_category_id'],
            'updated_at' => now(),
        ]);

        TaskStepActivity::create([
            'task_step_id'     => $this->step->id,
            'user_id'     => Auth::user()->id,
            'type'        => 'category_change',
            'description' => Auth::user()->name.' alterou a categoria',
        ]);
        
        $this->flashSuccess('Categoria atualizada.');
        $this->step->refresh();
    }

    public function updatedListPriorityId()
    {
        $data = $this->validate(TaskStepRules::priority());

        $this->step->update([
            'task_priority_id' => $data['list_priority_id'],
        ]);

        TaskStepActivity::create([
            'task_step_id'     => $this->step->id,
            'user_id'     => Auth::user()->id,
            'type'        => 'priority_change',
            'description' => Auth::user()->name.' alterou a prioridade',
        ]);
        
        $this->flashSuccess('Prioridade atualizada.');
        $this->step->refresh();
    }

    public function updatedListStatusId()
    {
        $data = $this->validate(TaskStepRules::status());

        if ($data['list_status_id'] == 3) {
            $this->step->update([
                'task_status_id' => $data['list_status_id'],
                'started_at' => now(),
            ]);
        } else {
            $this->step->update([
                'task_status_id' => $data['list_status_id'],
            ]);
        }

        TaskStepActivity::create([
            'task_step_id'     => $this->step->id,
            'user_id'     => Auth::user()->id,
            'type'        => 'status_change',
            'description' => Auth::user()->name.' alterou o status',
        ]);
        
        $this->flashSuccess('Status atualizado.');
        $this->step->refresh();
    }

    public function enableDescriptionEdit()
    {
        $this->isEditingDescription = true;
        $this->description = TaskStep::findOrFail($this->stepId)->description;
    }
    
    public function cancelDescriptionEdit()
    {
        $this->isEditingDescription = false;
        $this->description = TaskStep::findOrFail($this->stepId)->description;
        $this->savingDescription = false;
    }
    
    public function saveDescription()
    {
        $this->validate([
            'description' => 'nullable|string|max:1000',
        ]);
        
        $this->savingDescription = true;

        TaskStep::findOrFail($this->stepId)->update([
            'description' => str($this->description)->trim(),
            'updated_at' => now(),
        ]);

        $this->isEditingDescription = false;
        $this->savingDescription = false;
        
        $this->flashSuccess('Descrição atualizado.');
        $this->step->refresh();
    }

    public function enableDeadlineEdit()
    {
        $this->isEditingDeadline = true;
        $this->deadline_at = $this->step->deadline_at;
    }
    
    public function cancelDeadlineEdit()
    {
        $this->isEditingDeadline = false;
        $this->deadline_at = $this->step->deadline_at;
        $this->savingDeadline = false;
    }
    
    public function saveDeadline()
    {
        $data = $this->validate(TaskStepRules::deadlineAt());
        
        $this->savingDeadline = true;
        
        $this->step->update([
            'deadline_at' => $data['deadline_at'],
            'updated_at' => now(),
        ]);

        TaskStepActivity::create([
            'task_step_id'     => $this->step->id,
            'user_id'     => Auth::user()->id,
            'type'        => 'deadline_change',
            'description' => Auth::user()->name.' alterou o prazo',
        ]);

        $this->isEditingDeadline = false;
        $this->savingDeadline = false;
        
        $this->step->refresh();
        $this->flashSuccess('Prazo atualizado.');
    }

    public function storeComment()
    {
        $data = $this->validate(TaskStepRules::storeComment());

        $task = Task::findOrFail($this->step->task_id);

        $data['task_step_id'] = $this->step->id;
        $data['user_id'] = Auth::user()->id;
        $data['type'] = 'comment';
        $data['description'] = $data['comment'];

        TaskStepActivity::create($data);

        $task->update([
            'update_at' => now()
        ]);

        $this->comment = '';
        $this->step->refresh();
    }

    public function stepFinished()
    {
        $this->step->update([
            'task_status_id' => 6,
            'finished_at' => now(),
        ]); 

        TaskActivity::create([
            'task_id'     => $this->step->task->id,
            'user_id'     => Auth::user()->id,
            'type'        => 'step_finished_change',
            'description' => Auth::user()->name.' marcou a etapa '. $this->step->title .' como concluída',
        ]);

        TaskStepActivity::create([
            'task_step_id'     => $this->step->id,
            'user_id'     => Auth::user()->id,
            'type'        => 'finished_change',
            'description' => Auth::user()->name.' marcou a etapa como concluída',
        ]);

        $this->flashSuccess('Tarefa marcada como concluída.');
        $this->step->refresh();
    }

    public function render()
    {
        return view('livewire.task.task-step-aside');
    }
}
