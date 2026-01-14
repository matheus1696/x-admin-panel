<?php

namespace App\Livewire\Workflow;

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

    protected $rules = [
        'title' => 'required|string|max:255',
        'deadline_days'  => 'required|integer|min:1',
    ];

    private function resetForm()
    {
        $this->reset(['title', 'deadline_days']);
        $this->resetValidation();
    }

    public function mount($workflowId)
    {
        $this->workflowId = $workflowId;
        $this->loadActivities();
    }

    public function loadActivities()
    {
        $this->workflowStages = WorkflowStage::where('workflow_id', $this->workflowId)->orderBy('order')->get();
    }

    public function store(WorkflowStageService $workflowStageService)
    {
        $data = $this->validate();
        $data['workflow_id'] = $this->workflowId;
        $data['order'] = $this->workflowStages->count() + 1;

        $workflowStageService->create($data);

        TaskType::find($this->workflowId)->increment('days', $this->deadline_days);

        $this->resetForm();
        $this->flashSuccess('Ativiade criado com sucesso.');
        $this->loadActivities();
    }

    public function edit(WorkflowStage $workflowStage)
    {
        $this->workflowStageId = $workflowStage->id;
        $this->title = $workflowStage->title;
        $this->deadline_days = $workflowStage->deadline_days;
        $this->order = $workflowStage->order;
    }

    public function update(WorkflowStageService $workflowStageService)
    {
        $data = $this->validate();

        $workflowStage = WorkflowStage::findOrFail($this->workflowStageId);

        if ($workflowStage->deadline_days > $this->deadline_days) {
            TaskType::find($this->workflowId)->decrement('days', $workflowStage->deadline_days - $this->deadline_days);
        } else {
            TaskType::find($this->workflowId)->increment('days', $this->deadline_days - $workflowStage->deadline_days);
        }      

        $workflowStageService->update($this->workflowStageId, $data);

        $this->resetForm();
        $this->flashSuccess('Atividade alterada com sucesso.');
        $this->workflowStageId = null;

        $this->loadActivities();
    }

    public function orderUp(WorkflowStage $workflowStage)
    {
        $workflowStages = WorkflowStage::where('workflow_id', $workflowStage->workflow_id)->get();

        if ($workflowStage->order > 1) {
            $workflowStage->order -= 1;
            $workflowStage->save();
        }

        foreach ($workflowStages as $item) {
            if ($item->id != $workflowStage->id && $item->order >= $workflowStage->order && $item->order < $workflowStage->order + 1) {
                $item->order += 1;
                $item->save();
            }
        }

        $this->loadActivities();
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
