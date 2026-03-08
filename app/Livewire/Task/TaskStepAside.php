<?php

namespace App\Livewire\Task;

use App\Livewire\Traits\WithFlashMessage;
use App\Models\Administration\Task\TaskPriority;
use App\Models\Administration\Task\TaskStepCategory;
use App\Models\Task\TaskStep;
use App\Services\Administration\Task\TaskStepStatusService;
use App\Services\Task\TaskService;
use App\Support\Notifications\InteractsWithSystemNotifications;
use App\Validation\Task\TaskStepRules;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TaskStepAside extends Component
{
    use InteractsWithSystemNotifications;
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

        $this->users = collect();
        $this->organizations = collect();
        $this->taskPriorities = TaskPriority::orderBy('level')->get();
        $this->taskStepCategories = TaskStepCategory::orderBy('title')->get();
        $this->taskStepStatuses = collect();
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
        $this->taskStepStatuses = $this->taskStepStatusesService->index($this->step->task_hub_id);
        $this->syncUsersForOrganization($this->step->organization_id);
        $this->isLoading = false;
    }

    public function updatedOrganizationResponsableId()
    {
        $allowedOrganizationIds = $this->organizations
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();
        $previousOrganizationId = (int) ($this->step->organization_id ?? 0);

        $data = $this->validate(TaskStepRules::organizationResponsable($allowedOrganizationIds));

        $this->responsable_id = null;

        $this->step = $this->taskService->updateStepOrganizationResponsible(
            $this->step->id,
            $data['organization_responsable_id']
        );

        $this->syncUsersForOrganization($data['organization_responsable_id']);
        $this->usersKey++;
        $this->dispatch('$refresh');

        $this->flashSuccess('Responsavel atualizado.');

        if (
            $this->step->organization
            && (int) $this->step->organization->id !== $previousOrganizationId
            && $this->step->organization->users->isNotEmpty()
        ) {
            $this->notifyUsers(
                $this->step->organization->users,
                'Seu setor foi associado a uma tarefa',
                'A etapa '.$this->step->code.' da tarefa '.($this->step->task?->code ?? 'sem codigo').' foi direcionada ao setor '.$this->step->organization->title.'.',
                [
                    'url' => route('tasks.show', $this->step->task?->taskHub?->uuid ?? $this->step->taskHub->uuid),
                    'icon' => 'fa-solid fa-sitemap',
                    'level' => 'info',
                ]
            );
        }
    }

    public function updatedResponsableId()
    {
        $allowedUserIds = $this->resolveAllowedUserIds();

        $data = $this->validate(TaskStepRules::responsable($allowedUserIds));

        $this->step = $this->taskService->updateStepResponsible($this->step->id, $data['responsable_id']);

        $this->flashSuccess('Responsavel atualizado.');
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

        $this->step = $this->taskService->updateStepCategory($this->step->id, $data['list_category_id']);

        $this->flashSuccess('Categoria atualizada.');
    }

    public function updatedListPriorityId()
    {
        $data = $this->validate(TaskStepRules::priority());

        $this->step = $this->taskService->updateStepPriority($this->step->id, $data['list_priority_id']);

        $this->flashSuccess('Prioridade atualizada.');
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
            $this->flashError('Nao e possivel iniciar esta etapa enquanto a etapa anterior obrigatoria do fluxo estiver aberta.');

            return;
        }

        $this->step = $updatedStep;

        $this->flashSuccess('Status atualizado.');
        $this->step->refresh();
    }

    public function enableDescriptionEdit()
    {
        $this->isEditingDescription = true;
        $this->description = $this->step->description;
    }

    public function cancelDescriptionEdit()
    {
        $this->isEditingDescription = false;
        $this->description = $this->step->description;
        $this->savingDescription = false;
    }

    public function saveDescription()
    {
        $this->validate([
            'description' => 'nullable|string|max:1000',
        ]);

        $this->savingDescription = true;

        $this->step = $this->taskService->updateStepDescription($this->step->id, trim((string) $this->description));

        $this->isEditingDescription = false;
        $this->savingDescription = false;

        $this->flashSuccess('Descricao atualizada.');
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

        $this->step = $this->taskService->updateStepDeadline($this->step->id, $data['deadline_at']);

        $this->isEditingDeadline = false;
        $this->savingDeadline = false;

        $this->flashSuccess('Prazo atualizado.');
    }

    public function storeStepComment()
    {
        $data = $this->validate(TaskStepRules::storeComment());

        $this->taskService->storeStepComment($this->step->id, $data['comment']);

        $this->comment = '';
        $this->step->refresh();
    }

    public function stepFinished()
    {
        $data = $this->validate(TaskStepRules::storeComment());

        $this->taskService->completeStep($this->step->id, $data['comment']);

        $this->comment = '';
        $this->flashSuccess('Etapa marcada como concluida.');
        $this->step->refresh();
    }

    public function render()
    {
        return view('livewire.task.task-step-aside');
    }
}
