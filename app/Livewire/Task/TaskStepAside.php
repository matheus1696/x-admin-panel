<?php

namespace App\Livewire\Task;

use App\Models\Task\TaskStep;
use Livewire\Component;

class TaskStepAside extends Component
{
    public $stepId;

    public function render()
    {
        return view('livewire.task.task-step-aside',[
            'step' => TaskStep::find($this->stepId),
        ]);
    }
}
