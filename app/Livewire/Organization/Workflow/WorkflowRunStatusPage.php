<?php

namespace App\Livewire\Organization\Workflow;

use App\Livewire\Traits\Modal;
use App\Models\Organization\Workflow\WorkflowRunStatus;
use App\Models\Organization\Workflow\WorkflowRunStepStatus;
use Livewire\Component;

class WorkflowRunStatusPage extends Component
{
    use Modal;
    
    public function render()
    {
        $workflowRunStatuses = WorkflowRunStatus::all();
        $workflowRunStepStatuses = WorkflowRunStepStatus::all();

        return view('livewire.organization.workflow.workflow-run-status-page',[
            'workflowRunStatuses' => $workflowRunStatuses,
            'workflowRunStepStatuses' => $workflowRunStepStatuses,
        ])->layout('layouts.app');
    }
}
