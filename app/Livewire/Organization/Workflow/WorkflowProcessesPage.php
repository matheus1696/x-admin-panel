<?php

namespace App\Livewire\Organization\Workflow;

use App\Http\Requests\Organization\Workflow\WorkflowStoreRequest;
use App\Http\Requests\Organization\Workflow\WorkflowUpdateRequest;
use App\Livewire\Traits\Modal;
use App\Livewire\Traits\WithFlashMessage;
use App\Models\Organization\Workflow\Workflow;
use App\Services\Organization\Workflow\WorkflowService;
use Livewire\Component;
use Livewire\WithPagination;

class WorkflowProcessesPage extends Component
{
    use WithPagination, WithFlashMessage, Modal;

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
        $this->openModal('modal-form-create-workflow');
    }

    public function store(WorkflowService $WorkflowService)
    {
        $data = $this->validate((new WorkflowStoreRequest())->rules());

        $WorkflowService->create($data);

        $this->flashSuccess('Tipo de tarefa criado com sucesso.');
        $this->closeModal();
    }

    public function edit(Workflow $Workflow)
    {
        $this->resetForm();

        $this->workflowId = $Workflow->id;
        $this->title = $Workflow->title;
        $this->description = $Workflow->description;

        $this->openModal('modal-form-edit-workflow');
    }

    public function update(WorkflowService $WorkflowService)
    {
        $data = $this->validate((new WorkflowUpdateRequest())->rules());

        $WorkflowService->update($this->workflowId, $data);

        $this->flashSuccess('Tipo de tarefa foi atualizada com sucesso.');
        $this->closeModal();
    }

    public function status(WorkflowService $WorkflowService, Workflow $Workflow)
    {
        $WorkflowService->status($Workflow->id);
        $this->flashSuccess('Tipo de tarefa foi atualizada com sucesso.');
    }

    public function workflowStage(Workflow $Workflow)
    {
        $this->workflowId = $Workflow->id;
        $this->openModal('modal-form-workflow-steps');
    }

    public function render()
    {
        $query = Workflow::query();

        if ($this->filters['workflow']) {
            $query->where('filter', 'like', '%' . strtolower($this->filters['workflow']) . '%');
        }

        if ($this->filters['status'] !== 'all') {
            $query->where('status', $this->filters['status']);
        }

        $workflows = $query->orderBy('title')->paginate($this->filters['perPage']);

        return view('livewire.organization.workflow.workflow-processes-page', [
            'workflows' => $workflows,
        ])->layout('layouts.app');
    }
}
