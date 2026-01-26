<?php

namespace App\Livewire\Administration\Task;

use App\Livewire\Traits\Modal;
use App\Livewire\Traits\WithFlashMessage;
use App\Services\Administration\Task\TaskStatusService;
use App\Services\Administration\Task\TaskStepStatusService;
use App\Validation\Administration\Task\TaskStatusRules;
use App\Validation\Administration\Task\TaskStepStatusRules;
use Livewire\Component;

class TaskStatusPage extends Component
{
    use Modal, WithFlashMessage;

    protected TaskStatusService $taskStatusService;
    protected TaskStepStatusService $taskStepStatusService;

    public int $workflowRunStatusId;
    public int $workflowRunStepStatusId;

    public string $title;
    public string $color;

    public function boot(TaskStatusService $taskStatusService, TaskStepStatusService $taskStepStatusService)
    {
        $this->taskStatusService = $taskStatusService;
        $this->taskStepStatusService = $taskStepStatusService;
    }

    public function createRunStatus()
    {
        $this->reset();
        $this->openModal('modal-form-create-task-status');
    }

    public function storeRunStatus()
    {
        $data = $this->validate(TaskStatusRules::store());

        $this->taskStatusService->create($data);

        $this->flashSuccess('Status criado com sucesso.');
        $this->closeModal();
    }

    public function editRunStatus(int $id)
    {
        $this->reset();

        $workflowRunStatus = $this->taskStatusService->find($id);

        $this->workflowRunStatusId = $workflowRunStatus->id;
        $this->title = $workflowRunStatus->title;
        $this->color = $workflowRunStatus->color;

        $this->openModal('modal-form-edit-task-status');
    }

    public function updateRunStatus()
    {
        $data = $this->validate(TaskStatusRules::update());

        $this->taskStatusService->update($this->workflowRunStatusId, $data);

        $this->flashSuccess('Status atualizado com sucesso.');
        $this->closeModal();
    }      

    public function createRunStepStatus()
    {
        $this->reset();
        $this->openModal('modal-form-create-task-step-status');
    }

    public function storeRunStepStatus()
    {
        $data = $this->validate(TaskStepStatusRules::store());

        $this->taskStepStatusService->create($data);

        $this->flashSuccess('Status criado com sucesso.');
        $this->closeModal();
    }

    public function editRunStepStatus(int $id)
    {
        $this->reset();

        $workflowRunStepStatus = $this->taskStepStatusService->find($id);

        $this->workflowRunStepStatusId = $workflowRunStepStatus->id;
        $this->title = $workflowRunStepStatus->title;
        $this->color = $workflowRunStepStatus->color;

        $this->openModal('modal-form-edit-task-step-status');
    }

    public function updateRunStepStatus()
    {
        $data = $this->validate(TaskStepStatusRules::update());

        $this->taskStepStatusService->update($this->workflowRunStepStatusId, $data);

        $this->flashSuccess('Status atualizado com sucesso.');
        $this->closeModal();
    }   
    
    public function render()
    {
        $taskStatuses = $this->taskStatusService->index();
        $taskStepStatuses = $this->taskStepStatusService->index();

        return view('livewire.administration.task.task-status-page',[
            'taskStatuses' => $taskStatuses,
            'taskStepStatuses' => $taskStepStatuses,
        ])->layout('layouts.app');
    }
}
