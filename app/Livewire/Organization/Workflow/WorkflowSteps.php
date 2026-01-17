<?php

namespace App\Livewire\Organization\Workflow;

use App\Http\Requests\Organization\Workflow\WorkflowStepStoreRequest;
use App\Http\Requests\Organization\Workflow\WorkflowStepUpdateRequest;
use App\Livewire\Traits\WithFlashMessage;
use App\Models\Organization\Workflow\WorkflowStep;
use App\Services\Organization\Workflow\WorkflowStepService;
use Livewire\Component;

class WorkflowSteps extends Component
{
    use WithFlashMessage;

    public $workflowId;
    public $workflowSteps;
    public $title;
    public $deadline_days;
    public $workflowStepId;

    private function resetForm()
    {
        $this->reset(['title', 'deadline_days']);
        $this->resetValidation();
    }

    public function mount($workflowId)
    {
        $this->workflowId = $workflowId;
        $this->loadWorkflowStep();
    }

    public function loadWorkflowStep()
    {
        $this->workflowSteps = WorkflowStep::where('workflow_id', $this->workflowId)->orderBy('order')->get();
    }

    public function store(WorkflowStepService $workflowStepService)
    {
        $data = $this->validate((new WorkflowStepStoreRequest())->rules());
        $data['workflow_id'] = $this->workflowId;
        $data['order'] = $this->workflowSteps->count() + 1;

        $workflowStepService->create($data);

        $this->resetForm();
        $this->flashSuccess('Ativiade criado com sucesso.');
        $this->loadWorkflowStep();
    }

    public function edit(WorkflowStep $workflowStep)
    {
        $this->workflowStepId = $workflowStep->id;
        $this->title = $workflowStep->title;
        $this->deadline_days = $workflowStep->deadline_days;
    }

    public function update(WorkflowStepService $workflowStepService)
    {
        $data = $this->validate((new WorkflowStepUpdateRequest())->rules());

        $workflowStepService->update($this->workflowStepId, $data);

        $this->workflowStepId = null;
        $this->resetForm();
        $this->flashSuccess('Atividade alterada com sucesso.');

        $this->loadWorkflowStep();
    }

    public function orderUp(WorkflowStepService $workflowStepService, WorkflowStep $workflowStep)
    {
        $workflowStepService->order($workflowStep);
        $this->loadWorkflowStep();
    }

    public function closedUpdate()
    {
        $this->resetForm();
        $this->workflowStepId = null;
    }

    public function render()
    {
        return view('livewire.organization.workflow.workflow-steps');
    }
}
