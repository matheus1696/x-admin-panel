<?php

namespace App\Livewire\Task;

use App\Livewire\Traits\Modal;
use App\Livewire\Traits\WithFlashMessage;
use App\Models\Administration\User\User;
use App\Models\Task\TaskHub;
use App\Models\Task\TaskHubMember;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class TaskHubPage extends Component
{
    use Modal, WithFlashMessage;

    public string $taskHubId;
    public string $title = '';
    public string $acronym = '';
    public ?string $description = null;
    public ?int $shareTaskHubId = null;
    public ?int $member_user_id = null;

    public Collection $users;

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

        $taskHub = TaskHub::create([
            'title' => $validated['title'],
            'acronym' => strtoupper($validated['acronym']),
            'description' => $validated['description'] ?? null,
            'owner_id' => Auth::user()->id,
        ]);

        TaskHubMember::firstOrCreate([
            'task_hub_id' => $taskHub->id,
            'user_id' => Auth::user()->id,
        ]);

        $this->flashSuccess('Usuário adicionado com sucesso.');
        $this->closeModal();
    }

    public function openShareModal(int $taskHubId): void
    {
        $this->shareTaskHubId = $taskHubId;
        $this->member_user_id = null;
        $this->openModal('modal-task-hub-share');
    }

    public function closedShareModal(int $taskHubId): void
    {
        $this->shareTaskHubId = $taskHubId;
        $this->member_user_id = null;
        $this->closeModal();
    }

    public function addMember(): void
    {
        if (!$this->shareTaskHubId) {
            return;
        }

        $data = $this->validate([
            'member_user_id' => 'required|exists:users,id',
        ]);

        $taskHub = TaskHub::query()
            ->whereKey($this->shareTaskHubId)
            ->where('owner_id', Auth::user()->id)
            ->firstOrFail();

        TaskHubMember::firstOrCreate([
            'task_hub_id' => $taskHub->id,
            'user_id' => $data['member_user_id'],
        ]);

        $this->member_user_id = null;
        $this->flashSuccess('Usuário compartilhado com sucesso.');
    }

    public function removeMember(int $memberId): void
    {
        $member = TaskHubMember::query()
            ->whereKey($memberId)
            ->whereHas('taskHub', function ($query): void {
                $query->where('owner_id', Auth::user()->id);
            })
            ->firstOrFail();

        $member->delete();

        $this->flashSuccess('Usuário removido do ambiente.');
    }

    public function render()
    {
        $userId = Auth::user()->id;

        $taskHubs = TaskHub::query()
            ->with(['members.user'])
            ->where(function ($query) use ($userId): void {
                $query->where('owner_id', $userId)
                    ->orWhereHas('members', function ($memberQuery) use ($userId): void {
                        $memberQuery->where('user_id', $userId);
                    });
            })
            ->get();

        $this->users = User::query()->orderBy('name')->get();

        return view('livewire.task.task-hub-page', compact('taskHubs'));
    }
}