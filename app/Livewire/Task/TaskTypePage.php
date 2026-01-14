<?php

namespace App\Livewire\Task;

use App\Http\Requests\Task\TaskTypeStoreRequest;
use App\Http\Requests\Task\TaskTypeUpdateRequest;
use App\Livewire\Traits\Modal;
use App\Livewire\Traits\WithFlashMessage;
use App\Models\Task\TaskType;
use App\Services\Task\TaskTypeService;
use Livewire\Component;
use Livewire\WithPagination;

class TaskTypePage extends Component
{
    use WithPagination;
    use WithFlashMessage;
    use Modal;

    public array $filters = [
        'type' => '',
        'status' => 'all',
        'perPage' => 10,
    ];

    public $taskTypeId = null;
    public $title = '';
    public $description = '';

    public function updatedFilters()
    {
        $this->resetPage();
    }

    private function resetForm()
    {
        $this->reset(['taskTypeId', 'title', 'description']);
        $this->resetValidation();
    }

    public function create()
    {
        $this->resetForm();
        $this->openModal('modal-form-create-task-type');
    }

    public function store(TaskTypeService $taskTypeService)
    {
        $data = $this->validate((new TaskTypeStoreRequest())->rules());

        $taskTypeService->create($data);

        $this->flashSuccess('Tipo de tarefa criado com sucesso.');
        $this->closeModal();
    }

    public function edit(TaskType $taskType)
    {
        $this->resetForm();

        $this->taskTypeId = $taskType->id;
        $this->title = $taskType->title;
        $this->description = $taskType->description;

        $this->openModal('modal-form-edit-task-type');
    }

    public function update(TaskTypeService $taskTypeService)
    {
        $data = $this->validate((new TaskTypeUpdateRequest())->rules());

        $taskTypeService->update($this->taskTypeId, $data);

        $this->flashSuccess('Tipo de tarefa foi atualizada com sucesso.');
        $this->closeModal();
    }

    public function status(TaskTypeService $taskTypeService, TaskType $taskType)
    {
        $taskTypeService->status($taskType->id);
        $this->flashSuccess('Tipo de tarefa foi atualizada com sucesso.');
        $this->closeModal();
    }

    public function taskTypeActivity(TaskType $taskType)
    {
        $this->taskTypeId = $taskType->id;
        $this->openModal('modal-task-type-activity');
    }

    public function render()
    {
        $query = TaskType::query();

        if ($this->filters['type']) {
            $query->where('filter', 'like', '%' . strtolower($this->filters['type']) . '%');
        }

        if ($this->filters['status'] !== 'all') {
            $query->where('status', $this->filters['status']);
        }

        $taskTypes = $query->orderBy('title')->paginate($this->filters['perPage']);

        return view('livewire.task.task-type-page', compact('taskTypes'))->layout('layouts.app');
    }
}
