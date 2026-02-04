<?php

namespace App\Livewire\Task;

use App\Models\Task\Task;
use Livewire\Component;

class TaskAside extends Component
{
    public $taskId;

    public function render()
    {
        return view('livewire.task.task-aside',[
            'task' => Task::find($this->taskId),
        ]);
    }
}
