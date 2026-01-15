<?php

namespace App\Livewire\Workflow;

use App\Http\Requests\Workflow\WorkflowStageStoreRequest;
use App\Http\Requests\Workflow\WorkflowStageUpdateRequest;
use App\Livewire\Traits\WithFlashMessage;
use App\Models\Workflow\WorkflowStage;
use App\Services\Workflow\WorkflowStageService;
use Livewire\Component;

class WorkflowStagePage extends Component
{
    use WithFlashMessage;

    public $workflowId;
    public $workflowStages;
    public $title;
    public $deadline_days;
    public $workflowStageId;

    private function resetForm()
    {
        $this->reset(['title', 'deadline_days']);
        $this->resetValidation();
    }

    public function mount($workflowId)
    {
        $this->workflowId = $workflowId;
        $this->loadWorkflowStage();
    }

    public function loadWorkflowStage()
    {
        $this->workflowStages = WorkflowStage::where('workflow_id', $this->workflowId)->orderBy('order')->get();
    }

    public function store(WorkflowStageService $workflowStageService)
    {
        $data = $this->validate((new WorkflowStageStoreRequest())->rules());
        $data['workflow_id'] = $this->workflowId;
        $data['order'] = $this->workflowStages->count() + 1;

        $workflowStageService->create($data);

        $this->resetForm();
        $this->flashSuccess('Ativiade criado com sucesso.');
        $this->loadWorkflowStage();
    }

    public function edit(WorkflowStage $workflowStage)
    {
        $this->workflowStageId = $workflowStage->id;
        $this->title = $workflowStage->title;
        $this->deadline_days = $workflowStage->deadline_days;
    }

    public function update(WorkflowStageService $workflowStageService)
    {
        $data = $this->validate((new WorkflowStageUpdateRequest())->rules());

        $workflowStageService->update($this->workflowStageId, $data);

        $this->workflowStageId = null;
        $this->resetForm();
        $this->flashSuccess('Atividade alterada com sucesso.');

        $this->loadWorkflowStage();
    }

    public function orderUp(WorkflowStageService $workflowStageService, WorkflowStage $workflowStage)
    {
        $workflowStageService->order($workflowStage);
        $this->loadWorkflowStage();
    }

    public function closedUpdate()
    {
        $this->resetForm();
        $this->workflowStageId = null;
    }

    public function render()
    {
        return view('livewire.workflow.workflow-stage-page');
    }
}
