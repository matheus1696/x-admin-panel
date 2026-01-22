<?php

namespace App\Livewire\Organization\Workflow;

use App\Livewire\Traits\Modal;
use App\Livewire\Traits\WithFlashMessage;
use App\Models\Organization\Workflow\Workflow;
use App\Models\Organization\Workflow\WorkflowRunStatus;
use App\Services\Organization\Workflow\WorkflowRunService;
use App\Validation\Organization\Workflow\WorkflowRunRules;
use Livewire\Component;
use Livewire\WithPagination;

class WorkflowRunPage extends Component
{
    use Modal, WithFlashMessage, WithPagination;

    protected WorkflowRunService $workflowRunService;

    public array $filters = [
        'title' => '',
        'workflow_run_status_id' => 'all',
        'perPage' => 10,
    ];

    public $title;
    public $description;
    public $workflow_id;

    public function boot(WorkflowRunService $workflowRunService){
        $this->workflowRunService = $workflowRunService;
    }

    public function updatedFilters()
    {
        $this->resetPage();
    }
    
    public function create()
    {
        $this->reset();
        $this->openModal('modal-form-create-workflow-run');
    }

    public function store()
    {
        $data = $this->validate(WorkflowRunRules::store());

        $this->workflowRunService->create($data);

        $this->flashSuccess('Tarefa criada com sucesso.');
        $this->closeModal();
    }

    public function render()
    {
        $workflows = Workflow::all();
        $workflowRuns = $this->workflowRunService->index($this->filters);
        $workflowRunStatuses = WorkflowRunStatus::all();

        return view('livewire.organization.workflow.workflow-run-page',[
            'workflows' => $workflows,
            'workflowRuns' => $workflowRuns,
            'workflowRunStatuses' => $workflowRunStatuses
        ])->layout('layouts.app');
    }
}
