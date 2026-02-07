<?php

namespace App\Livewire\Task;

use App\Livewire\Traits\WithFlashMessage;
use App\Models\Administration\Task\TaskPriority;
use App\Models\Administration\Task\TaskStepCategory;
use App\Models\Administration\Task\TaskStepStatus;
use App\Models\Administration\User\User;
use App\Models\Organization\OrganizationChart\OrganizationChart;
use App\Models\Task\TaskStep;
use Livewire\Component;

class TaskStepList extends Component
{
    use WithFlashMessage;

    public int $stepId;

    public $responsable_id;
    public $responsable_organization_id;
    public $list_priority_id;
    public $list_task_step_status_id;

    public function updatedResponsableOrganizationId()
    {
        TaskStep::where('id', $this->stepId)->update([
            'organization_id' => $this->responsable_organization_id,
        ]);
        
        $this->flashSuccess('Setor responsável atualizado.');
    }

    public function updatedResponsableId()
    {
        TaskStep::where('id', $this->stepId)->update([
            'user_id' => $this->responsable_id,
        ]);
        
        $this->flashSuccess('Usuário responsável atualizado.');
    }

    public function updatedListPriorityId()
    {
        TaskStep::where('id', $this->stepId)->update([
            'task_priority_id' => $this->list_priority_id,
        ]);
        
        $this->flashSuccess('Prioridade atualizada.');
    }

    public function updatedListTaskStepStatusId()
    {
        TaskStep::where('id', $this->stepId)->update([
            'task_status_id' => $this->list_task_step_status_id,
        ]);
        
        $this->flashSuccess('Status atualizado.');
    }

    public function render()
    {
        return view('livewire.task.task-step-list',[
            'step' => TaskStep::find($this->stepId),
            'users' => User::orderBy('name')->get(),
            'taskPriorities' => TaskPriority::orderBy('level')->get(),
            'taskStepStatuses' => TaskStepStatus::orderBy('title')->get(),
            'taskStepCategories' => TaskStepCategory::orderBy('title')->get(),
            'organizations' => OrganizationChart::orderBy('hierarchy')->get(),
        ]);
    }
}
