<?php

namespace App\Livewire\Task;

use App\Models\Task\Task;
use Livewire\Component;

class TaskAside extends Component
{
    public $taskId;
    public $isEditingDescription = false;
    public $description = '';
    public $savingDescription = false;

    public function enableDescriptionEdit()
    {
        $this->isEditingDescription = true;
        $this->description = Task::findOrFail($this->taskId)->description;
    }
    
    public function cancelDescriptionEdit()
    {
        $this->isEditingDescription = false;
        $this->description = Task::findOrFail($this->taskId)->description;
        $this->savingDescription = false;
    }
    
    public function saveDescription()
    {
        $this->validate([
            'description' => 'nullable|string|max:1000',
        ]);
        
        $this->savingDescription = true;

        Task::findOrFail($this->taskId)->update([
            'description' => $this->description,
            'updated_at' => now(),
        ]);

        $this->isEditingDescription = false;
        $this->savingDescription = false;
    }

    public function render()
    {
        return view('livewire.task.task-aside',[
            'task' => Task::find($this->taskId),
        ]);
    }
}
