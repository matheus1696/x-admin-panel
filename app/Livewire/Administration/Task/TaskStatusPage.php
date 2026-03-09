<?php

namespace App\Livewire\Administration\Task;

use App\Livewire\Traits\Modal;
use App\Livewire\Traits\WithFlashMessage;
use App\Models\Task\TaskHub;
use App\Services\Administration\Task\TaskStatusService;
use App\Services\Administration\Task\TaskStepStatusService;
use App\Validation\Administration\Task\TaskStatusRules;
use App\Validation\Administration\Task\TaskStepStatusRules;
use Illuminate\Support\Collection;
use Livewire\Component;

class TaskStatusPage extends Component
{
    use Modal, WithFlashMessage;

    protected TaskStatusService $taskStatusService;
    protected TaskStepStatusService $taskStepStatusService;

    public ?int $workflowRunStatusId = null;
    public ?int $workflowRunStepStatusId = null;
    public ?int $taskHubId = null;
    public Collection $taskHubs;

    public string $title = '';
    public string $color = '';

    public function boot(TaskStatusService $taskStatusService, TaskStepStatusService $taskStepStatusService)
    {
        $this->taskStatusService = $taskStatusService;
        $this->taskStepStatusService = $taskStepStatusService;
    }

    public function mount(): void
    {
        $this->taskHubs = TaskHub::query()
            ->orderBy('title')
            ->get();

        $this->taskHubId = $this->taskHubs->first()?->id;
    }

    private function resetForm(): void
    {
        $this->reset(['workflowRunStatusId', 'workflowRunStepStatusId', 'title', 'color']);
        $this->resetValidation();
    }

    public function createRunStatus()
    {
        if (! $this->taskHubId) {
            $this->flashError('Selecione um ambiente de tarefas para continuar.');

            return;
        }

        $this->resetForm();
        $this->openModal('modal-form-create-task-status');
    }

    public function storeRunStatus()
    {
        if (! $this->taskHubId) {
            $this->flashError('Selecione um ambiente de tarefas para continuar.');

            return;
        }

        $data = $this->validate(TaskStatusRules::store());
        [$colorName, $tailwindColor] = $this->resolveColorStyle($data['color']);

        $this->taskStatusService->createForHub((int) $this->taskHubId, [
            'title' => trim($data['title']),
            'color' => $colorName,
            'color_code_tailwind' => $tailwindColor,
            'is_default' => false,
            'is_active' => true,
        ]);

        $this->flashSuccess('Status criado com sucesso.');
        $this->closeModal();
    }

    public function editRunStatus(int $id)
    {
        if (! $this->taskHubId) {
            $this->flashError('Selecione um ambiente de tarefas para continuar.');

            return;
        }

        $this->resetForm();

        $workflowRunStatus = $this->taskStatusService->find($id, (int) $this->taskHubId);

        $this->workflowRunStatusId = $workflowRunStatus->id;
        $this->title = $workflowRunStatus->title;
        $this->color = $workflowRunStatus->color_code_tailwind ?: $this->tailwindFromColorName($workflowRunStatus->color);

        $this->openModal('modal-form-edit-task-status');
    }

    public function updateRunStatus()
    {
        if (! $this->taskHubId || ! $this->workflowRunStatusId) {
            return;
        }

        $data = $this->validate(TaskStatusRules::update());
        [$colorName, $tailwindColor] = $this->resolveColorStyle($data['color']);

        $this->taskStatusService->update($this->workflowRunStatusId, [
            'title' => trim($data['title']),
            'color' => $colorName,
            'color_code_tailwind' => $tailwindColor,
        ], (int) $this->taskHubId);

        $this->flashSuccess('Status atualizado com sucesso.');
        $this->closeModal();
    }      

    public function createRunStepStatus()
    {
        if (! $this->taskHubId) {
            $this->flashError('Selecione um ambiente de tarefas para continuar.');

            return;
        }

        $this->resetForm();
        $this->openModal('modal-form-create-task-step-status');
    }

    public function storeRunStepStatus()
    {
        if (! $this->taskHubId) {
            $this->flashError('Selecione um ambiente de tarefas para continuar.');

            return;
        }

        $data = $this->validate(TaskStepStatusRules::store());
        [$colorName, $tailwindColor] = $this->resolveColorStyle($data['color']);

        $this->taskStepStatusService->createForHub((int) $this->taskHubId, [
            'title' => trim($data['title']),
            'color' => $colorName,
            'color_code_tailwind' => $tailwindColor,
            'is_default' => false,
            'is_active' => true,
        ]);

        $this->flashSuccess('Status criado com sucesso.');
        $this->closeModal();
    }

    public function editRunStepStatus(int $id)
    {
        if (! $this->taskHubId) {
            $this->flashError('Selecione um ambiente de tarefas para continuar.');

            return;
        }

        $this->resetForm();

        $workflowRunStepStatus = $this->taskStepStatusService->find($id, (int) $this->taskHubId);

        $this->workflowRunStepStatusId = $workflowRunStepStatus->id;
        $this->title = $workflowRunStepStatus->title;
        $this->color = $workflowRunStepStatus->color_code_tailwind ?: $this->tailwindFromColorName($workflowRunStepStatus->color);

        $this->openModal('modal-form-edit-task-step-status');
    }

    public function updateRunStepStatus()
    {
        if (! $this->taskHubId || ! $this->workflowRunStepStatusId) {
            return;
        }

        $data = $this->validate(TaskStepStatusRules::update());
        [$colorName, $tailwindColor] = $this->resolveColorStyle($data['color']);

        $this->taskStepStatusService->update($this->workflowRunStepStatusId, [
            'title' => trim($data['title']),
            'color' => $colorName,
            'color_code_tailwind' => $tailwindColor,
        ], (int) $this->taskHubId);

        $this->flashSuccess('Status atualizado com sucesso.');
        $this->closeModal();
    }

    /**
     * @return array{0:string, 1:string}
     */
    private function resolveColorStyle(string $tailwindColor): array
    {
        return match ($tailwindColor) {
            'bg-blue-100 text-blue-700 hover:bg-blue-200' => ['blue', $tailwindColor],
            'bg-green-100 text-green-700 hover:bg-green-200' => ['green', $tailwindColor],
            'bg-yellow-100 text-yellow-800 hover:bg-yellow-200' => ['yellow', $tailwindColor],
            'bg-red-100 text-red-700 hover:bg-red-200' => ['red', $tailwindColor],
            default => ['gray', 'bg-gray-100 text-gray-700 hover:bg-gray-200'],
        };
    }

    private function tailwindFromColorName(?string $color): string
    {
        return match ($color) {
            'blue' => 'bg-blue-100 text-blue-700 hover:bg-blue-200',
            'green' => 'bg-green-100 text-green-700 hover:bg-green-200',
            'yellow' => 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200',
            'red' => 'bg-red-100 text-red-700 hover:bg-red-200',
            default => 'bg-gray-100 text-gray-700 hover:bg-gray-200',
        };
    }

    public function render()
    {
        $taskStatuses = collect();
        $taskStepStatuses = collect();

        if ($this->taskHubId) {
            $taskStatuses = $this->taskStatusService->index((int) $this->taskHubId, false);
            $taskStepStatuses = $this->taskStepStatusService->index((int) $this->taskHubId, false);
        }

        return view('livewire.administration.task.task-status-page',[
            'taskStatuses' => $taskStatuses,
            'taskStepStatuses' => $taskStepStatuses,
            'taskHubs' => $this->taskHubs,
        ])->layout('layouts.app');
    }
}
