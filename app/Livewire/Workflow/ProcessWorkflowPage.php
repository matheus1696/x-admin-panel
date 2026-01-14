<?php

namespace App\Livewire\Workflow;

use App\Http\Requests\Task\WorkflowUpdateRequest;
use App\Http\Requests\Workflow\WorkflowStoreRequest;
use App\Livewire\Traits\Modal;
use App\Livewire\Traits\WithFlashMessage;
use App\Models\Workflow\ProcessWorkflow;
use App\Services\Task\ProcessWorkflowService;
use Livewire\Component;
use Livewire\WithPagination;

class ProcessWorkflowPage extends Component
{
    use WithPagination;
    use WithFlashMessage;
    use Modal;

    public array $filters = [
        'workflow' => '',
        'status' => 'all',
        'perPage' => 10,
    ];

    public $workflowId = null;
    public $title = '';
    public $description = '';

    public function updatedFilters()
    {
        $this->resetPage();
    }

    private function resetForm()
    {
        $this->reset(['workflowId', 'title', 'description']);
        $this->resetValidation();
    }

    public function create()
    {
        $this->resetForm();
        $this->openModal('modal-form-create-task-type');
    }

    public function store(ProcessWorkflowService $processWorkflowService)
    {
        $data = $this->validate((new WorkflowStoreRequest())->rules());

        $processWorkflowService->create($data);

        $this->flashSuccess('Tipo de tarefa criado com sucesso.');
        $this->closeModal();
    }

    public function edit(ProcessWorkflow $processWorkflow)
    {
        $this->resetForm();

        $this->workflowId = $processWorkflow->id;
        $this->title = $processWorkflow->title;
        $this->description = $processWorkflow->description;

        $this->openModal('modal-form-edit-task-type');
    }

    public function update(ProcessWorkflowService $processWorkflowService)
    {
        $data = $this->validate((new WorkflowUpdateRequest())->rules());

        $processWorkflowService->update($this->workflowId, $data);

        $this->flashSuccess('Tipo de tarefa foi atualizada com sucesso.');
        $this->closeModal();
    }

    public function status(ProcessWorkflowService $processWorkflowService, ProcessWorkflow $processWorkflow)
    {
        $processWorkflowService->status($processWorkflow->id);
        $this->flashSuccess('Tipo de tarefa foi atualizada com sucesso.');
        $this->closeModal();
    }

    public function taskTypeActivity(ProcessWorkflow $processWorkflow)
    {
        $this->workflowId = $processWorkflow->id;
        $this->openModal('modal-task-type-activity');
    }

    public function render()
    {
        $query = ProcessWorkflow::query();

        if ($this->filters['workflow']) {
            $query->where('filter', 'like', '%' . strtolower($this->filters['workflow']) . '%');
        }

        if ($this->filters['status'] !== 'all') {
            $query->where('status', $this->filters['status']);
        }

        $taskTypes = $query->orderBy('title')->paginate($this->filters['perPage']);

        return view('livewire.workflow.process-workflow-page', compact('taskTypes'))->layout('layouts.app');
    }
}
