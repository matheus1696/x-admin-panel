<?php

namespace App\Livewire\Task;

use App\Livewire\Traits\WithFlashMessage;
use App\Models\Task\TaskType;
use App\Models\Task\TaskTypeActivity;
use Livewire\Component;

class TaskTypeActivityPage extends Component
{ 
    use WithFlashMessage;

    public $taskTypeId;
    public $activities;
    public $title;
    public $deadline_days;
    public $order;
    public $activityId;
    public $success = false;

    protected $rules = [
        'title' => 'required|string|max:255',
        'deadline_days'  => 'required|integer|min:1',
    ];

    private function resetForm()
    {
        $this->reset(['title', 'deadline_days']);
        $this->resetValidation();
    }

    public function mount($taskTypeId)
    {
        $this->taskTypeId = $taskTypeId;
        $this->loadActivities();
    }

    public function loadActivities()
    {
        $this->activities = TaskTypeActivity::where('task_type_id', $this->taskTypeId)->orderBy('order')->get();
    }

    public function store()
    {
        $this->validate();

        TaskTypeActivity::create([
            'task_type_id' => $this->taskTypeId,
            'title' => $this->title,
            'deadline_days' => $this->deadline_days,
            'order' => $this->activities->count() + 1,
        ]);

        TaskType::find($this->taskTypeId)->increment('days', $this->deadline_days);

        $this->resetForm();
        $this->flashSuccess('Tipo de tarefa criado com sucesso.');

        $this->loadActivities();
    }

    public function edit($id)
    {
        $activity = TaskTypeActivity::findOrFail($id);
        $this->activityId = $id;
        $this->title = $activity->title;
        $this->deadline_days = $activity->deadline_days;
        $this->order = $activity->order;
    }

    public function update()
    {
        $this->validate();

        $activity = TaskTypeActivity::findOrFail($this->activityId);

        if ($activity->deadline_days > $this->deadline_days) {
            TaskType::find($this->taskTypeId)->decrement('days', $activity->deadline_days - $this->deadline_days);
        } else {
            TaskType::find($this->taskTypeId)->increment('days', $this->deadline_days - $activity->deadline_days);
        }      

        $activity->update([
            'title' => $this->title,
            'deadline_days' => $this->deadline_days,
        ]);

        $this->resetForm();
        $this->flashSuccess('Tipo de tarefa criado com sucesso.');

        $this->loadActivities();
    }

    public function render()
    {
        return view('livewire.task.task-type-activity-page');
    }
}
