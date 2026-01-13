<?php

namespace App\Livewire\Task;

use App\Models\Task\TaskType;
use Livewire\Component;

class TaskTypePage extends Component
{
    public $taskTypeId = null;

    public $title;
    public $description;
    public $status = true;

    public $showModal = false;

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string|max:500',
        'status' => 'boolean',
    ];

    public function create()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(TaskType $taskType)
    {
        $this->title = $taskType->title;
        $this->description = $taskType->description;

        $this->showModal = true;
    }

    public function store()
    {
        $this->validate();

        TaskType::create([
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
        ]);

        session()->flash('success', 'Task type created successfully.');

        $this->closeModal();
    }

    public function update()
    {
        $this->validate();

        TaskType::find($this->taskTypeId)->update([
            'title' => $this->title,
            'description' => $this->description,
        ]);

        session()->flash('success', 'Task type updated successfully.');

        $this->closeModal();
    }

    public function closeModal()
    {
        $this->resetForm();
        $this->showModal = false;
    }

    private function resetForm()
    {
        $this->reset(['taskTypeId', 'title', 'description', 'status']);
        $this->resetValidation();
    }

    public function render()
    {
        $taskTypes = TaskType::orderBy('title')->get();
        return view('livewire.task.task-type-page', compact('taskTypes'))->layout('layouts.app');
    }
}
