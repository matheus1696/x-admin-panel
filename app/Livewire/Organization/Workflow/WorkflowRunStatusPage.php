<?php

namespace App\Livewire\Organization\Workflow;

use App\Livewire\Traits\Modal;
use App\Livewire\Traits\WithFlashMessage;
use App\Models\Organization\Workflow\WorkflowRunStatus;
use App\Models\Organization\Workflow\WorkflowRunStepStatus;
use App\Services\Organization\Workflow\WorkflowRunStatusService;
use App\Services\Organization\Workflow\WorkflowRunStepStatusService;
use App\Validation\Organization\Workflow\WorkflowRunStatusRules;
use App\Validation\Organization\Workflow\WorkflowRunStepStatusRules;
use Livewire\Component;

class WorkflowRunStatusPage extends Component
{
    use Modal, WithFlashMessage;

    protected WorkflowRunStatusService $workflowRunStatusService;
    protected WorkflowRunStepStatusService $workflowRunStepStatusService;

    public int $workflowRunStatusId;
    public int $workflowRunStepStatusId;

    public string $title;
    public string $color;

    public function boot(WorkflowRunStatusService $workflowRunStatusService, WorkflowRunStepStatusService $workflowRunStepStatusService)
    {
        $this->workflowRunStatusService = $workflowRunStatusService;
        $this->workflowRunStepStatusService = $workflowRunStepStatusService;
    }

    public function createRunStatus()
    {
        $this->reset();
        $this->openModal('modal-form-create-workflow-run-status');
    }

    public function storeRunStatus()
    {
        $data = $this->validate(WorkflowRunStatusRules::store());

        $this->workflowRunStatusService->create($data);

        $this->flashSuccess('Status criado com sucesso.');
        $this->closeModal();
    }

    public function editRunStatus(int $id)
    {
        $this->reset();

        $workflowRunStatus = $this->workflowRunStatusService->find($id);

        $this->workflowRunStatusId = $workflowRunStatus->id;
        $this->title = $workflowRunStatus->title;
        $this->color = $workflowRunStatus->color;

        $this->openModal('modal-form-edit-workflow-run-status');
    }

    public function updateRunStatus()
    {
        $data = $this->validate(WorkflowRunStatusRules::update());

        $this->workflowRunStatusService->update($this->workflowRunStatusId, $data);

        $this->flashSuccess('Status atualizado com sucesso.');
        $this->closeModal();
    }      

    public function createRunStepStatus()
    {
        $this->reset();
        $this->openModal('modal-form-create-workflow-run-step-status');
    }

    public function storeRunStepStatus()
    {
        $data = $this->validate(WorkflowRunStepStatusRules::store());

        $this->workflowRunStepStatusService->create($data);

        $this->flashSuccess('Status criado com sucesso.');
        $this->closeModal();
    }

    public function editRunStepStatus(int $id)
    {
        $this->reset();

        $workflowRunStepStatus = $this->workflowRunStepStatusService->find($id);

        $this->workflowRunStepStatusId = $workflowRunStepStatus->id;
        $this->title = $workflowRunStepStatus->title;
        $this->color = $workflowRunStepStatus->color;

        $this->openModal('modal-form-edit-workflow-run-step-status');
    }

    public function updateRunStepStatus()
    {
        $data = $this->validate(WorkflowRunStepStatusRules::update());

        $this->workflowRunStepStatusService->update($this->workflowRunStepStatusId, $data);

        $this->flashSuccess('Status atualizado com sucesso.');
        $this->closeModal();
    }   
    
    public function render()
    {
        $workflowRunStatuses = $this->workflowRunStatusService->index();
        $workflowRunStepStatuses = $this->workflowRunStepStatusService->index();

        return view('livewire.organization.workflow.workflow-run-status-page',[
            'workflowRunStatuses' => $workflowRunStatuses,
            'workflowRunStepStatuses' => $workflowRunStepStatuses,
        ])->layout('layouts.app');
    }
}
