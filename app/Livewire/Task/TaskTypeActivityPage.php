<?php

namespace App\Livewire\Task;

use App\Livewire\Traits\WithFlashMessage;
use App\Models\Task\TaskType;
use App\Models\Task\TaskTypeActivity;
use App\Services\Task\TaskTypeActivityService;
use Livewire\Component;

class TaskTypeActivityPage extends Component
{ 
    use WithFlashMessage;

    public $taskTypeId;
    public $activities;
    public $title;
    public $deadline_days;
    public $activityId;

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

    public function store(TaskTypeActivityService $taskTypeActivityService)
    {
        $data = $this->validate();
        $data['task_type_id'] = $this->taskTypeId;
        $data['order'] = $this->activities->count() + 1;

        $taskTypeActivityService->create($data);

        TaskType::find($this->taskTypeId)->increment('days', $this->deadline_days);

        $this->resetForm();
        $this->flashSuccess('Ativiade criado com sucesso.');
        $this->loadActivities();
    }

    public function edit(TaskTypeActivity $activity)
    {
        $this->activityId = $activity->id;
        $this->title = $activity->title;
        $this->deadline_days = $activity->deadline_days;
        $this->order = $activity->order;
    }

    public function update(TaskTypeActivityService $taskTypeActivityService)
    {
        $data = $this->validate();

        $activity = TaskTypeActivity::findOrFail($this->activityId);

        if ($activity->deadline_days > $this->deadline_days) {
            TaskType::find($this->taskTypeId)->decrement('days', $activity->deadline_days - $this->deadline_days);
        } else {
            TaskType::find($this->taskTypeId)->increment('days', $this->deadline_days - $activity->deadline_days);
        }      

        $taskTypeActivityService->update($this->activityId, $data);

        $this->resetForm();
        $this->flashSuccess('Atividade alterada com sucesso.');
        $this->activityId = null;

        $this->loadActivities();
    }

    public function orderUp(TaskTypeActivity $taskTypeActivity)
    {
        $taskTypeActivities = TaskTypeActivity::where('task_type_id', $taskTypeActivity->task_type_id)->get();

        if ($taskTypeActivity->order > 1) {
            $taskTypeActivity->order -= 1;
            $taskTypeActivity->save();
        }

        foreach ($taskTypeActivities as $item) {
            if ($item->id != $taskTypeActivity->id && $item->order >= $taskTypeActivity->order && $item->order < $taskTypeActivity->order + 1) {
                $item->order += 1;
                $item->save();
            }
        }

        $this->loadActivities();
    }

    public function closedUpdate()
    {
        $this->resetForm();
        $this->activityId = null;
    }

    public function render()
    {
        return view('livewire.task.task-type-activity-page');
    }
}
