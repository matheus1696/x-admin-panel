<?php

namespace App\Livewire\Task;

use App\Livewire\Traits\Modal;
use App\Livewire\Traits\WithFlashMessage;
use App\Models\Task\TaskHub;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class TaskHubPage extends Component
{
    use Modal, WithFlashMessage;

    public string $title = '';
    public string $acronym = '';
    public ?string $description = null;

    /* CREATE */
    public function create(): void
    {
        $this->openModal('modal-task-hub');
    }

    public function store(): void
    {
        $validated = $this->validate([
            'title' => 'required|string|max:255',
            'acronym' => 'required|string|uppercase|max:5',
            'description' => 'nullable|string|max:1000',
        ]);

        TaskHub::create([
            'title' => $validated['title'],
            'acronym' => strtoupper($validated['acronym']),
            'description' => $validated['description'] ?? null,
            'owner_id' => auth()->id(),
        ]);
        
        $this->flashSuccess('Usuário adicionado com sucesso.');
        $this->closeModal();
    }

    public function render()
    {
        $taskHubs = TaskHub::all();

        return view('livewire.task.task-hub-page', compact('taskHubs'));
    }
}
