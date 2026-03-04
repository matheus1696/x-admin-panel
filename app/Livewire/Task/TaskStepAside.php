<?php

namespace App\Livewire\Task;

use App\Livewire\Traits\WithFlashMessage;
use App\Models\Administration\Task\TaskPriority;
use App\Models\Administration\Task\TaskStepCategory;
use App\Models\Task\Task;
use App\Models\Task\TaskStep;
use App\Models\Task\TaskStepActivity;
use App\Services\Administration\Task\TaskStepStatusService;
use App\Services\Task\TaskService;
use App\Validation\Task\TaskStepRules;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

use function Livewire\str;

class TaskStepAside extends Component
{
    use WithFlashMessage;

    protected TaskStepStatusService $taskStepStatusesService;

    protected TaskService $taskService;

    public $stepId;

    public $step;

    public Collection $users;

    public Collection $organizations;

    public Collection $taskPriorities;

    public Collection $taskStepCategories;

    public Collection $taskStepStatuses;

    public $description = '';

    public $responsable_id;

    public $organization_responsable_id;

    public $list_category_id;

    public $list_priority_id;

    public $list_status_id;

    public $deadline_at = null;

    public $comment;

    public $isEditingDescription = false;

    public $savingDescription = false;

    public $isEditingDeadline = false;

    public $savingDeadline = false;

    public $isLoading = true;

    public int $usersKey = 0;

    public function boot(TaskStepStatusService $taskStepStatusesService, TaskService $taskService)
    {
        $this->taskStepStatusesService = $taskStepStatusesService;
        $this->taskService = $taskService;
    }

    public function mount($stepId)
    {
        $this->isLoading = true;
        $this->step = null;
        $this->stepId = $stepId;

        // Listas estáticas
        $this->users = collect();
        $this->organizations = collect();
        $this->taskPriorities = TaskPriority::orderBy('level')->get();
        $this->taskStepCategories = TaskStepCategory::orderBy('title')->get();
        $this->taskStepStatuses = $this->taskStepStatusesService->index();
        $this->loadStep();
    }

    public function loadStep()
    {
        $this->step = TaskStep::with([
            'organization',
            'taskPriority',
            'taskStepStatus',
            'taskStepCategory',
            'user',
            'stepActivities.user',
        ])->findOrFail($this->stepId);
        $this->users = $this->taskService->accessUsersByHubId($this->step->task_hub_id);
        $this->organizations = $this->taskService->organizationAccessesByHubId($this->step->task_hub_id);
        $this->syncUsersForOrganization($this->step->organization_id);
        $this->isLoading = false;
    }

    public function updatedOrganizationResponsableId()
    {
        $allowedOrganizationIds = $this->organizations
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();

        $data = $this->validate(TaskStepRules::organizationResponsable($allowedOrganizationIds));

        $this->responsable_id = null;

        $this->step->update([
            'organization_id' => $data['organization_responsable_id'],
            'user_id' => null,
            'updated_at' => now(),
        ]);

        $this->syncUsersForOrganization($data['organization_responsable_id']);
        $this->usersKey++;
        $this->dispatch('$refresh');

        TaskStepActivity::create([
            'task_step_id' => $this->step->id,
            'user_id' => Auth::user()->id,
            'type' => 'organization_responsable_change',
            'description' => Auth::user()->name.' alterou o responsável',
        ]);

        $this->flashSuccess('Responsável atualizado.');
        $this->step->refresh();
    }

    public function updatedResponsableId()
    {
        $allowedUserIds = $this->resolveAllowedUserIds();

        $data = $this->validate(TaskStepRules::responsable($allowedUserIds));

        $this->step->update([
            'user_id' => $data['responsable_id'],
            'updated_at' => now(),
        ]);

        TaskStepActivity::create([
            'task_step_id' => $this->step->id,
            'user_id' => Auth::user()->id,
            'type' => 'responsable_change',
            'description' => Auth::user()->name.' alterou o responsável',
        ]);

        $this->flashSuccess('Responsável atualizado.');
        $this->step->refresh();
    }

    private function resolveAllowedUserIds(): array
    {
        $organizationId = $this->organization_responsable_id
            ? (int) $this->organization_responsable_id
            : ($this->step?->organization_id ? (int) $this->step->organization_id : null);

        if ($organizationId !== null) {
            $organization = $this->organizations->firstWhere('id', $organizationId);

            if ($organization) {
                return $organization->users
                    ->pluck('id')
                    ->map(fn ($id): int => (int) $id)
                    ->all();
            }
        }

        return $this->users
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();
    }

    private function syncUsersForOrganization(?int $organizationId): void
    {
        if ($organizationId === null) {
            $this->users = $this->taskService->accessUsersByHubId($this->step->task_hub_id);
            $this->usersKey++;

            return;
        }

        $organization = $this->organizations->firstWhere('id', (int) $organizationId);

        if (! $organization) {
            $this->users = $this->taskService->accessUsersByHubId($this->step->task_hub_id);
            $this->usersKey++;

            return;
        }

        $this->users = $organization->users
            ->sortBy(fn ($user) => $user->name ?? '')
            ->values();
        $this->usersKey++;
    }

    public function getFilteredUsersProperty(): Collection
    {
        $organizationId = $this->organization_responsable_id
            ? (int) $this->organization_responsable_id
            : ($this->step?->organization_id ? (int) $this->step->organization_id : null);

        if ($organizationId !== null) {
            $organization = $this->organizations->firstWhere('id', $organizationId);

            if ($organization) {
                return $organization->users
                    ->sortBy(fn ($user) => $user->name ?? '')
                    ->values();
            }
        }

        return $this->users;
    }

    public function updatedListCategoryId()
    {
        $data = $this->validate(TaskStepRules::category());

        $this->step->update([
            'task_category_id' => $data['list_category_id'],
            'updated_at' => now(),
        ]);

        TaskStepActivity::create([
            'task_step_id' => $this->step->id,
            'user_id' => Auth::user()->id,
            'type' => 'category_change',
            'description' => Auth::user()->name.' alterou a categoria',
        ]);

        $this->flashSuccess('Categoria atualizada.');
        $this->step->refresh();
    }

    public function updatedListPriorityId()
    {
        $data = $this->validate(TaskStepRules::priority());

        $this->step->update([
            'task_priority_id' => $data['list_priority_id'],
        ]);

        TaskStepActivity::create([
            'task_step_id' => $this->step->id,
            'user_id' => Auth::user()->id,
            'type' => 'priority_change',
            'description' => Auth::user()->name.' alterou a prioridade',
        ]);

        $this->flashSuccess('Prioridade atualizada.');
        $this->step->refresh();
    }

    public function updatedListStatusId()
    {
        $data = $this->validate(TaskStepRules::status());

        $updatedStep = $this->taskService->changeStepStatus(
            $this->step->id,
            $data['list_status_id'],
            Auth::user()->name.' alterou o status'
        );

        if (! $updatedStep) {
            $this->step->refresh();
            $this->flashError('Não é possível iniciar esta etapa enquanto a etapa anterior obrigatória do fluxo estiver aberta.');

            return;
        }

        $this->step = $updatedStep;

        $this->flashSuccess('Status atualizado.');
        $this->step->refresh();
    }

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
            'description' => str($this->description)->trim(),
            'updated_at' => now(),
        ]);

        $this->isEditingDescription = false;
        $this->savingDescription = false;

        $this->flashSuccess('Descrição atualizado.');
        $this->step->refresh();
    }

    public function enableDeadlineEdit()
    {
        $this->isEditingDeadline = true;
        $this->deadline_at = $this->step->deadline_at;
    }

    public function cancelDeadlineEdit()
    {
        $this->isEditingDeadline = false;
        $this->deadline_at = $this->step->deadline_at;
        $this->savingDeadline = false;
    }

    public function saveDeadline()
    {
        $data = $this->validate(TaskStepRules::deadlineAt());

        $this->savingDeadline = true;

        $this->step->update([
            'deadline_at' => $data['deadline_at'],
            'updated_at' => now(),
        ]);

        TaskStepActivity::create([
            'task_step_id' => $this->step->id,
            'user_id' => Auth::user()->id,
            'type' => 'deadline_change',
            'description' => Auth::user()->name.' alterou o prazo',
        ]);

        $this->isEditingDeadline = false;
        $this->savingDeadline = false;

        $this->step->refresh();
        $this->flashSuccess('Prazo atualizado.');
    }

    public function storeStepComment()
    {
        $data = $this->validate(TaskStepRules::storeComment());

        $task = Task::findOrFail($this->step->task_id);

        $data['task_step_id'] = $this->step->id;
        $data['user_id'] = Auth::user()->id;
        $data['type'] = 'comment';
        $data['description'] = $data['comment'];

        TaskStepActivity::create($data);

        $task->update([
            'update_at' => now(),
        ]);

        $this->comment = '';
        $this->step->refresh();
    }

    public function stepFinished()
    {
        $data = $this->validate(TaskStepRules::storeComment());

        $this->taskService->completeStep($this->step->id, $data['comment']);

        $this->comment = '';
        $this->flashSuccess('Etapa marcada como concluída.');
        $this->step->refresh();
    }

    public function render()
    {
        return view('livewire.task.task-step-aside');
    }
}
