<?php

namespace App\Livewire\Task;

use App\Livewire\Traits\Modal;
use App\Livewire\Traits\WithFlashMessage;
use App\Services\Task\TaskService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class TaskHubPage extends Component
{
    use Modal, WithFlashMessage;

    protected TaskService $taskService;

    public string $taskHubId;
    public string $title = '';
    public string $acronym = '';
    public ?string $description = null;

    public function boot(TaskService $taskService): void
    {
        $this->taskService = $taskService;
    }

    /* CREATE */
    public function create(): void
    {
        $this->reset();
        $this->openModal('modal-task-hub');
    }

    public function store(): void
    {
        $validated = $this->validate([
            'title' => 'required|string|max:255',
            'acronym' => 'required|string|uppercase|max:5',
            'description' => 'nullable|string|max:1000',
        ]);

        $this->taskService->createHub($validated, (int) Auth::id());

        $this->flashSuccess('Ambiente criado com sucesso.');
        $this->closeModal();
    }

    public function render()
    {
        $userId = Auth::user()->id;

        $taskHubs = $this->taskService->accessibleHubs($userId);

        return view('livewire.task.task-hub-page', compact('taskHubs'));
    }
}



