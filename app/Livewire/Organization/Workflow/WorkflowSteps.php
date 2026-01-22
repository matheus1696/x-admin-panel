<?php

namespace App\Livewire\Organization\Workflow;

use App\Http\Requests\Organization\Workflow\WorkflowStepStoreRequest;
use App\Http\Requests\Organization\Workflow\WorkflowStepUpdateRequest;
use App\Livewire\Traits\WithFlashMessage;
use App\Models\Organization\Workflow\WorkflowStep;
use App\Services\Organization\Workflow\WorkflowStepService;
use App\Validation\Organization\Workflow\WorkflowStepRules;
use Livewire\Component;

class WorkflowSteps extends Component
{
    use WithFlashMessage;

    protected WorkflowStepService $workflowStepService;

    public $workflowId;
    public $workflowStepId;

    public $workflowSteps;

    public $title;
    public $deadline_days;
    public bool $required = true;
    public bool $allow_parallel = false;

    public function boot(WorkflowStepService $workflowStepService)
    {
        $this->workflowStepService = $workflowStepService;
    }

    private function resetForm()
    {
        $this->reset(['title', 'deadline_days', 'required', 'allow_parallel',]);
        $this->resetValidation();
    }

    public function mount($workflowId)
    {
        $this->workflowId = $workflowId;
        $this->loadWorkflowStep();
    }

    public function loadWorkflowStep()
    {
        $this->workflowSteps = $this->workflowStepService->listByWorkflow($this->workflowId);
    }

    public function store()
    {
        $data = $this->validate(WorkflowStepRules::store());

        $data['workflow_id'] = $this->workflowId;
        $data['step_order'] = $this->workflowSteps->count() + 1;

        $this->workflowStepService->create($data);

        $this->resetForm();
        $this->flashSuccess('Ativiade criado com sucesso.');
        $this->loadWorkflowStep();
    }

    public function edit(int $id)
    {
        $this->resetForm();

        $workflowStep = $this->workflowStepService->find($id);

        $this->workflowStepId = $workflowStep->id;
        $this->title = $workflowStep->title;
        $this->deadline_days = $workflowStep->deadline_days;
        $this->required = $workflowStep->required;
        $this->allow_parallel = $workflowStep->allow_parallel;
    }

    public function update()
    {
        $data = $this->validate(WorkflowStepRules::update($this->workflowStepId));

        $this->workflowStepService->update($this->workflowStepId, $data);

        $this->workflowStepId = null;
        $this->resetForm();
        $this->flashSuccess('Atividade alterada com sucesso.');

        $this->loadWorkflowStep();
    }

    public function orderUp(int $id)
    {
        $this->workflowStepService->order($id);
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
