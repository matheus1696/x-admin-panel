<?php

namespace App\Livewire\Organization\Workflow;

use Livewire\Component;

class WorkflowRunStatus extends Component
{
    public function render()
    {
        return view('livewire.organization.workflow.workflow-run-status')->layout('layouts.app');
    }
}
