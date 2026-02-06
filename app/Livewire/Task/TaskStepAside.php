<?php

namespace App\Livewire\Task;

use App\Models\Task\TaskStep;
use Livewire\Component;

class TaskStepAside extends Component
{
    public $stepId;
    public $isEditingDescription = false;
    public $description = '';
    public $savingDescription = false;

    public function enableDescriptionEdit()
    {
        $this->isEditingDescription = true;
        $this->description = TaskStep::findOrFail($this->stepId)->description;
    }
    
    public function cancelDescriptionEdit()
    {
        $this->isEditingDescription = false;
        $this->description = TaskStep::findOrFail($this->stepId)->description;
        $this->savingDescription = false;
    }
    
    public function saveDescription()
    {
        $this->validate([
            'description' => 'nullable|string|max:1000',
        ]);
        
        $this->savingDescription = true;

        TaskStep::findOrFail($this->stepId)->update([
            'description' => $this->description,
            'updated_at' => now(),
        ]);

        $this->isEditingDescription = false;
        $this->savingDescription = false;
    }

    public function render()
    {
        return view('livewire.task.task-step-aside',[
            'step' => TaskStep::find($this->stepId),
        ]);
    }
}
