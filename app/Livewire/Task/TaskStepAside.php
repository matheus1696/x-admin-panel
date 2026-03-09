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
use Illuminate\Support\Str;
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

    public bool $showStatusReasonModal = false;

    public ?int $pendingStatusToId = null;

    public ?string $pendingStatusReasonType = null;

    public string $statusTransitionReason = '';

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
            'task.taskHub',
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
        $this->list_status_id = $this->step->task_status_id;
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
        $fromStatusId = (int) ($this->step->task_status_id ?? 0);
        $toStatusId = (int) ($data['list_status_id'] ?? 0);

        if ($this->taskService->isInvalidStepTerminalSwap(
            $this->step->task_hub_id,
            $fromStatusId === 0 ? null : $fromStatusId,
            $toStatusId === 0 ? null : $toStatusId
        )) {
            $this->list_status_id = $this->step->task_status_id;
            $this->flashError('Não é permitido mover uma etapa concluída para cancelada ou cancelada para concluída.');

            return;
        }

        $reasonType = $this->taskService->stepReasonTypeForTransition(
            $this->step->task_hub_id,
            $fromStatusId === 0 ? null : $fromStatusId,
            $toStatusId === 0 ? null : $toStatusId
        );

        if ($fromStatusId !== $toStatusId && $reasonType !== null) {
            $this->pendingStatusToId = $toStatusId;
            $this->pendingStatusReasonType = $reasonType;
            $this->statusTransitionReason = '';
            $this->showStatusReasonModal = true;
            $this->list_status_id = $this->step->task_status_id;

            return;
        }

        $this->applyStepStatusChange($toStatusId);
    }

    public function confirmStatusTransitionReason(): void
    {
        $this->validate([
            'statusTransitionReason' => ['required', 'string', 'max:2000'],
        ]);

        if ($this->pendingStatusToId === null) {
            $this->cancelStatusTransitionReason();

            return;
        }

        $this->applyStepStatusChange(
            $this->pendingStatusToId,
            trim($this->statusTransitionReason),
            $this->pendingStatusReasonType
        );

        $this->resetPendingStatusTransition();
    }

    public function cancelStatusTransitionReason(): void
    {
        $this->resetPendingStatusTransition();
        $this->list_status_id = $this->step->task_status_id;
    }

    private function applyStepStatusChange(int $toStatusId, ?string $reason = null, ?string $reasonType = null): void
    {
        $fromStatusId = (int) ($this->step->task_status_id ?? 0);
        $columns = collect($this->taskService->stepKanban($this->step->task->taskHub->uuid));
        $sourceColumn = $columns->firstWhere('status_id', $fromStatusId);
        $targetColumn = $columns->firstWhere('status_id', $toStatusId);

        $sourceOrder = collect($sourceColumn['steps'] ?? [])
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->reject(fn (int $id): bool => $id === (int) $this->step->id)
            ->values()
            ->all();

        $targetOrder = collect($targetColumn['steps'] ?? [])
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->reject(fn (int $id): bool => $id === (int) $this->step->id)
            ->push((int) $this->step->id)
            ->unique()
            ->values()
            ->all();

        $updated = $this->taskService->moveKanbanStep(
            $this->step->task->taskHub->uuid,
            (int) $this->step->id,
            $fromStatusId,
            $toStatusId,
            $sourceOrder,
            $targetOrder,
            $reason,
            $reasonType
        );

        if (! $updated) {
            $this->step->refresh();
            $this->list_status_id = $this->step->task_status_id;

            if (in_array($toStatusId, $this->stepInProgressStatusIds(), true)) {
                $this->flashError('Não é possível iniciar esta etapa enquanto a etapa anterior obrigatória do fluxo estiver aberta.');
            } else {
                $this->flashError('Não foi possível atualizar o status da etapa.');
            }

            return;
        }

        $this->loadStep();
        $this->flashSuccess('Status atualizado.');
    }

    /**
     * @return array<int>
     */
    private function stepInProgressStatusIds(): array
    {
        return $this->taskStepStatuses
            ->filter(function ($status): bool {
                $normalized = (string) Str::of((string) $status->title)->lower()->ascii();

                return str_contains($normalized, 'andamento') || str_contains($normalized, 'execu');
            })
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();
    }

    private function resetPendingStatusTransition(): void
    {
        $this->showStatusReasonModal = false;
        $this->pendingStatusToId = null;
        $this->pendingStatusReasonType = null;
        $this->statusTransitionReason = '';
        $this->resetValidation();
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
