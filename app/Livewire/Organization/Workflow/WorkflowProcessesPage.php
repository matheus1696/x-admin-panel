<?php

namespace App\Livewire\Organization\Workflow;

use App\Http\Requests\Organization\Workflow\WorkflowStoreRequest;
use App\Http\Requests\Organization\Workflow\WorkflowUpdateRequest;
use App\Livewire\Traits\Modal;
use App\Livewire\Traits\WithFlashMessage;
use App\Services\Organization\Workflow\WorkflowService;
use Livewire\Component;
use Livewire\WithPagination;

class WorkflowProcessesPage extends Component
{
    use WithPagination, WithFlashMessage, Modal;

    protected WorkflowService $workflowService;

    public array $filters = [
        'title' => '',
        'status' => 'all',
        'perPage' => 10,
    ];

    public $workflowId = null;
    public $title = '';
    public $description = '';

    public function boot(WorkflowService $workflowService)
    {
        $this->workflowService = $workflowService;
    }

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

    public function store()
    {
        $data = $this->validate((new WorkflowStoreRequest())->rules());

        $this->workflowService->create($data);

        $this->flashSuccess('Processo criado com sucesso.');
        $this->closeModal();
    }

    public function edit(int $id)
    {
        $this->resetForm();

        $workflow = $this->workflowService->find($id);

        $this->workflowId = $workflow->id;
        $this->title = $workflow->title;
        $this->description = $workflow->description;

        $this->openModal('modal-form-edit-workflow');
    }

    public function update()
    {
        $data = $this->validate((new WorkflowUpdateRequest())->rules());

        $this->workflowService->update($this->workflowId, $data);

        $this->flashSuccess('Tipo de tarefa foi atualizada com sucesso.');
        $this->closeModal();
    }

    public function status(int $id)
    {
        $this->workflowService->status($id);
        $this->flashSuccess('Tipo de tarefa foi atualizada com sucesso.');
    }

    public function workflowStep(int $id)
    {
        $this->workflowId = $id;
        $this->openModal('modal-form-workflow-steps');
    }

    public function render()
    {
        $workflows = $this->workflowService->index($this->filters);

        return view('livewire.organization.workflow.workflow-processes-page', [
            'workflows' => $workflows,
        ])->layout('layouts.app');
    }
}
