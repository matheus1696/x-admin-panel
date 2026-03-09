<?php

namespace App\Services\Task;

use App\Models\Administration\Task\TaskStatus;
use App\Models\Administration\Task\TaskStepStatus;
use App\Models\Administration\User\User;
use App\Models\Organization\OrganizationChart\OrganizationChart;
use App\Models\Organization\Workflow\Workflow;
use App\Models\Task\Task;
use App\Models\Task\TaskActivity;
use App\Models\Task\TaskHub;
use App\Models\Task\TaskHubMember;
use App\Models\Task\TaskStep;
use App\Models\Task\TaskStepActivity;
use App\Support\Notifications\InteractsWithSystemNotifications;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TaskService
{
    use InteractsWithSystemNotifications;

    public function createHub(array $data, int $ownerId): TaskHub
    {
        return DB::transaction(function () use ($data, $ownerId): TaskHub {
            $taskHub = TaskHub::create([
                'title' => $data['title'],
                'acronym' => strtoupper((string) $data['acronym']),
                'description' => $data['description'] ?? null,
                'owner_id' => $ownerId,
            ]);

            TaskHubMember::firstOrCreate([
                'task_hub_id' => $taskHub->id,
                'user_id' => $ownerId,
            ]);

            $this->createDefaultStatusesForHub($taskHub->id);

            return $taskHub;
        });
    }

    public function availableWorkflows(): Collection
    {
        return Workflow::query()
            ->where('is_active', true)
            ->with('workflowSteps.organization')
            ->orderBy('title')
            ->get();
    }

    public function members(string $hubUuid): Collection
    {
        $taskHub = TaskHub::query()
            ->with(['members.user'])
            ->where('uuid', $hubUuid)
            ->firstOrFail();

        return $taskHub->members
            ->sortBy(fn (TaskHubMember $member) => $member->user?->name ?? '')
            ->values();
    }

    public function memberUsers(string $hubUuid): Collection
    {
        return $this->members($hubUuid)
            ->map(fn (TaskHubMember $member) => $member->user)
            ->filter()
            ->values();
    }

    public function memberUsersByHubId(int $taskHubId): Collection
    {
        return TaskHubMember::query()
            ->with('user')
            ->where('task_hub_id', $taskHubId)
            ->get()
            ->map(fn (TaskHubMember $member) => $member->user)
            ->filter()
            ->sortBy(fn (User $user) => $user->name ?? '')
            ->values();
    }

    public function organizationAccesses(string $hubUuid): Collection
    {
        $taskHub = TaskHub::query()
            ->with('organizations.users')
            ->where('uuid', $hubUuid)
            ->firstOrFail();

        return $taskHub->organizations
            ->sortBy(fn (OrganizationChart $organization) => $organization->title ?? '')
            ->values();
    }

    public function organizationAccessesByHubId(int $taskHubId): Collection
    {
        $taskHub = TaskHub::query()
            ->with('organizations.users')
            ->findOrFail($taskHubId);

        return $taskHub->organizations
            ->sortBy(fn (OrganizationChart $organization) => $organization->title ?? '')
            ->values();
    }

    public function addOrganizationAccess(string $hubUuid, int $actorId, int $organizationId): bool
    {
        $taskHub = TaskHub::query()
            ->where('uuid', $hubUuid)
            ->firstOrFail();

        if ($taskHub->owner_id !== $actorId) {
            return false;
        }

        $taskHub->organizations()->syncWithoutDetaching([$organizationId]);

        return true;
    }

    public function removeOrganizationAccess(string $hubUuid, int $actorId, int $organizationId): bool
    {
        $taskHub = TaskHub::query()
            ->where('uuid', $hubUuid)
            ->firstOrFail();

        if ($taskHub->owner_id !== $actorId) {
            return false;
        }

        $taskHub->organizations()->detach($organizationId);

        return true;
    }

    public function accessUsers(string $hubUuid): Collection
    {
        $taskHub = TaskHub::query()
            ->with(['owner', 'members.user', 'organizations.users'])
            ->where('uuid', $hubUuid)
            ->firstOrFail();

        return $this->collectAccessUsers($taskHub);
    }

    public function accessUsersByHubId(int $taskHubId): Collection
    {
        $taskHub = TaskHub::query()
            ->with(['owner', 'members.user', 'organizations.users'])
            ->findOrFail($taskHubId);

        return $this->collectAccessUsers($taskHub);
    }

    /**
     * @return \Illuminate\Support\Collection<int, array{
     *     user:\App\Models\Administration\User\User,
     *     type:string,
     *     membership_id:int|null,
     *     sector_labels:array<int, string>,
     *     has_sector_access:bool
     * }>
     */
    public function accessUserEntries(string $hubUuid): Collection
    {
        $taskHub = TaskHub::query()
            ->with(['owner', 'members.user', 'organizations.users'])
            ->where('uuid', $hubUuid)
            ->firstOrFail();

        return $this->collectAccessUserEntries($taskHub);
    }

    public function findAccessibleHub(string $hubUuid, int $userId): TaskHub
    {
        return TaskHub::query()
            ->with(['members.user', 'organizations'])
            ->where('uuid', $hubUuid)
            ->where(function ($query) use ($userId): void {
                $query->where('owner_id', $userId)
                    ->orWhereHas('members', function ($memberQuery) use ($userId): void {
                        $memberQuery->where('user_id', $userId);
                    })
                    ->orWhereHas('organizations.users', function ($organizationQuery) use ($userId): void {
                        $organizationQuery->where('users.id', $userId);
                    });
            })
            ->firstOrFail();
    }

    public function accessibleHubs(int $userId): Collection
    {
        return TaskHub::query()
            ->with(['owner', 'members.user', 'organizations'])
            ->where(function ($query) use ($userId): void {
                $query->where('owner_id', $userId)
                    ->orWhereHas('members', function ($memberQuery) use ($userId): void {
                        $memberQuery->where('user_id', $userId);
                    })
                    ->orWhereHas('organizations.users', function ($organizationQuery) use ($userId): void {
                        $organizationQuery->where('users.id', $userId);
                    });
            })
            ->get();
    }

    public function addMember(string $hubUuid, int $actorId, int $memberUserId): bool
    {
        $taskHub = TaskHub::query()
            ->where('uuid', $hubUuid)
            ->firstOrFail();

        if ($taskHub->owner_id !== $actorId) {
            return false;
        }

        $membership = TaskHubMember::firstOrCreate([
            'task_hub_id' => $taskHub->id,
            'user_id' => $memberUserId,
        ]);

        if ($membership->wasRecentlyCreated) {
            $memberUser = User::query()->find($memberUserId);

            if ($memberUser) {
                $this->notifyUsers(
                    $memberUser,
                    'Voce foi associado a um ambiente de tarefas',
                    'Agora voce tem acesso ao ambiente '.$taskHub->title.'.',
                    [
                        'url' => route('tasks.show', $taskHub->uuid),
                        'icon' => 'fa-solid fa-layer-group',
                        'level' => 'info',
                    ]
                );
            }
        }

        return true;
    }

    public function removeMember(string $hubUuid, int $actorId, int $membershipId): bool
    {
        $taskHub = TaskHub::query()
            ->where('uuid', $hubUuid)
            ->firstOrFail();

        if ($taskHub->owner_id !== $actorId) {
            return false;
        }

        $membership = TaskHubMember::query()
            ->where('task_hub_id', $taskHub->id)
            ->findOrFail($membershipId);

        if ($membership->user_id === $taskHub->owner_id) {
            return false;
        }

        $membership->delete();

        return true;
    }

    public function addMembersByOrganization(string $hubUuid, int $actorId, int $organizationId): int
    {
        $taskHub = TaskHub::query()
            ->where('uuid', $hubUuid)
            ->firstOrFail();

        if ($taskHub->owner_id !== $actorId) {
            return 0;
        }

        $organization = OrganizationChart::query()
            ->with('users')
            ->findOrFail($organizationId);

        $userIds = $organization->users
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();

        if ($userIds === []) {
            return 0;
        }

        $existingUserIds = TaskHubMember::query()
            ->where('task_hub_id', $taskHub->id)
            ->whereIn('user_id', $userIds)
            ->pluck('user_id')
            ->map(fn ($id): int => (int) $id)
            ->all();

        $newUserIds = array_values(array_diff($userIds, $existingUserIds));

        foreach ($newUserIds as $userId) {
            TaskHubMember::firstOrCreate([
                'task_hub_id' => $taskHub->id,
                'user_id' => $userId,
            ]);
        }

        $users = User::query()
            ->whereIn('id', $newUserIds)
            ->get();

        if ($users->isNotEmpty()) {
            $this->notifyUsers(
                $users,
                'Voce foi associado a um ambiente de tarefas',
                'Seu acesso ao ambiente '.$taskHub->title.' foi liberado por vinculacao de setor.',
                [
                    'url' => route('tasks.show', $taskHub->uuid),
                    'icon' => 'fa-solid fa-layer-group',
                    'level' => 'info',
                ]
            );
        }

        return count($newUserIds);
    }

    public function find(int $id): Task
    {
        return Task::with([
            'taskPriority',
            'taskStatus',
            'taskCategory',
            'taskActivities.user',
            'taskSteps',
            'taskStepsFinished',
        ])->findOrFail($id);
    }

    public function copyWorkflowToTask(int $taskId, int $workflowId): bool
    {
        return DB::transaction(function () use ($taskId, $workflowId): bool {
            $task = Task::query()
                ->with('taskSteps')
                ->lockForUpdate()
                ->findOrFail($taskId);

            if ($task->taskSteps->isNotEmpty()) {
                return false;
            }

            $workflow = Workflow::query()
                ->where('is_active', true)
                ->with('workflowSteps')
                ->findOrFail($workflowId);

            if ($workflow->workflowSteps->isEmpty()) {
                return false;
            }

            $defaultStepStatusId = TaskStepStatus::query()
                ->where('task_hub_id', $task->task_hub_id)
                ->where('is_default', true)
                ->value('id');

            $baseDate = ($task->started_at ?? $task->created_at ?? now())->copy()->startOfDay();
            $accumulatedDays = 0;
            $finalDeadline = null;

            foreach ($workflow->workflowSteps->sortBy('step_order') as $workflowStep) {
                $accumulatedDays += max(0, (int) ($workflowStep->deadline_days ?? 0));
                $stepDeadline = $baseDate->copy()->addDays($accumulatedDays);
                $finalDeadline = $stepDeadline->copy();

                TaskStep::create([
                    'task_hub_id' => $task->task_hub_id,
                    'task_id' => $task->id,
                    'title' => $workflowStep->title,
                    'organization_id' => $workflowStep->organization_id,
                    'task_status_id' => $defaultStepStatusId,
                    'workflow_step_order' => (int) $workflowStep->step_order,
                    'is_required' => (bool) $workflowStep->required,
                    'allow_parallel' => (bool) $workflowStep->allow_parallel,
                    'deadline_at' => $stepDeadline,
                    'kanban_order' => $this->nextStepKanbanOrder($task->task_hub_id, $defaultStepStatusId),
                    'created_user_id' => Auth::id(),
                ]);
            }

            if ($finalDeadline !== null) {
                $task->update([
                    'deadline_at' => $finalDeadline,
                ]);
            }

            TaskActivity::create([
                'task_id' => $task->id,
                'user_id' => Auth::id(),
                'type' => 'workflow_copy',
                'description' => $this->actorName().' copiou o fluxo de trabalho '.$workflow->title.' para a tarefa',
                'meta' => [
                    'workflow_id' => $workflow->id,
                    'workflow_title' => $workflow->title,
                    'total_steps' => $workflow->workflowSteps->count(),
                ],
            ]);

            return true;
        });
    }

    public function index(string $id, array $filters): LengthAwarePaginator
    {
        $taskHub = TaskHub::where('uuid', $id)->firstOrFail();

        $query = Task::query();
        $organizationId = isset($filters['organization_id']) && $filters['organization_id'] !== ''
            ? (int) $filters['organization_id']
            : null;
        $userId = isset($filters['user_id']) && $filters['user_id'] !== ''
            ? (int) $filters['user_id']
            : null;
        $taskCategoryId = isset($filters['task_category_id']) && $filters['task_category_id'] !== ''
            ? (int) $filters['task_category_id']
            : null;
        $taskStatusId = isset($filters['task_status_id']) && $filters['task_status_id'] !== ''
            ? (int) $filters['task_status_id']
            : null;
        $taskPriorityId = isset($filters['task_priority_id']) && $filters['task_priority_id'] !== ''
            ? (int) $filters['task_priority_id']
            : null;
        $isOverdue = $filters['is_overdue'] ?? 'all';

        $query->where('task_hub_id', $taskHub->id)
            ->whereNull('finished_at');

        // Filtra pelo título
        if ($filters['title']) {
            $query->where('title', 'like', '%'.$filters['title'].'%');
        }

        if ($organizationId !== null) {
            $query->whereHas('taskSteps', function ($stepQuery) use ($organizationId): void {
                $stepQuery->where('organization_id', $organizationId);
            });
        }

        if ($userId !== null) {
            $query->where('user_id', $userId);
        }

        if ($taskCategoryId !== null) {
            $query->where('task_category_id', $taskCategoryId);
        }

        if ($taskStatusId !== null) {
            $query->where('task_status_id', $taskStatusId);
        }

        if ($taskPriorityId !== null) {
            $query->where('task_priority_id', $taskPriorityId);
        }

        if ($isOverdue === 'yes') {
            $query->whereNotNull('deadline_at')
                ->where('deadline_at', '<', now());
        }

        if ($isOverdue === 'no') {
            $query->where(function ($deadlineQuery): void {
                $deadlineQuery->whereNull('deadline_at')
                    ->orWhere('deadline_at', '>=', now());
            });
        }

        $query->with([
            'user',
            'taskCategory',
            'taskPriority',
            'taskStatus',
            'taskSteps.organization',
            'taskSteps.user',
            'taskSteps.taskPriority',
            'taskSteps.taskStepStatus',
        ]);

        return $query->orderBy('code')->paginate($filters['perPage']);
    }

    public function create(string $id, array $data): void
    {
        $taskHub = TaskHub::where('uuid', $id)->first();

        $statusId = $data['task_status_id'] ?? null;

        if ($statusId === null) {
            $statusId = TaskStatus::query()
                ->where('task_hub_id', $taskHub->id)
                ->where('is_default', true)
                ->value('id');
        }

        $data['task_status_id'] = $statusId;

        if ($statusId !== null && in_array((int) $statusId, $this->taskInProgressStatusIds($taskHub->id), true)) {
            $data['started_at'] = now();
        }

        $data['kanban_order'] = $this->nextKanbanOrder($taskHub->id, $statusId);
        $data['task_hub_id'] = $taskHub->id;
        $data['created_user_id'] = Auth::user()->id;
        $task = Task::create($data);

        TaskActivity::create([
            'task_id' => $task->id,
            'user_id' => Auth::user()->id,
            'type' => 'created',
            'description' => 'Tarefa criada por '.Auth::user()->name,
        ]);

        $task->refresh()->load(['taskHub', 'user']);

        if ($task->user) {
            $this->notifyUsers(
                $task->user,
                'Voce foi associado a uma tarefa',
                'A tarefa '.$task->code.' - '.$task->title.' foi atribuida a voce.',
                [
                    'url' => route('tasks.show', $task->taskHub->uuid),
                    'icon' => 'fa-solid fa-list-check',
                    'level' => 'info',
                ]
            );
        }
    }

    public function createStepForTask(int $taskHubId, int $taskId, array $data): TaskStep
    {
        $task = Task::query()
            ->where('task_hub_id', $taskHubId)
            ->findOrFail($taskId);

        $step = TaskStep::create([
            'task_hub_id' => $taskHubId,
            'task_id' => $task->id,
            'title' => $data['step_title'],
            'user_id' => $data['step_user_id'],
            'organization_id' => $data['organization_id'],
            'task_priority_id' => $data['step_task_priority_id'],
            'task_status_id' => $data['task_step_status_id'],
            'kanban_order' => $this->nextStepKanbanOrder($taskHubId, $data['task_step_status_id']),
            'created_user_id' => Auth::id(),
        ]);

        return $step->load(['task.taskHub', 'organization.users']);
    }

    public function update(int $id, array $data): void
    {
        $task = Task::findOrFail($id);
        $task->update($data);
    }

    public function storeComment(int $id, array $data, string $type): void
    {
        $task = Task::findOrFail($id);

        $data['task_id'] = $task->id;
        $data['user_id'] = Auth::user()->id;
        $data['type'] = $type;
        $data['description'] = $data['comment'];

        TaskActivity::create($data);

        $task->update([
            'updated_at' => now(),
        ]);
    }

    public function updateTaskResponsible(int $taskId, ?int $userId): Task
    {
        $task = Task::query()->findOrFail($taskId);

        $task->update([
            'user_id' => $userId,
            'updated_at' => now(),
        ]);

        TaskActivity::create([
            'task_id' => $task->id,
            'user_id' => Auth::user()->id,
            'type' => 'responsable_change',
            'description' => Auth::user()->name.' alterou o responsavel',
        ]);

        return $task->refresh()->load(['taskHub', 'user']);
    }

    public function updateTaskCategory(int $taskId, ?int $categoryId): Task
    {
        $task = Task::query()->findOrFail($taskId);

        $task->update([
            'task_category_id' => $categoryId,
            'updated_at' => now(),
        ]);

        TaskActivity::create([
            'task_id' => $task->id,
            'user_id' => Auth::user()->id,
            'type' => 'category_change',
            'description' => Auth::user()->name.' alterou a categoria',
        ]);

        return $task->refresh();
    }

    public function updateTaskPriority(int $taskId, ?int $priorityId): Task
    {
        $task = Task::query()->findOrFail($taskId);

        $task->update([
            'task_priority_id' => $priorityId,
            'updated_at' => now(),
        ]);

        TaskActivity::create([
            'task_id' => $task->id,
            'user_id' => Auth::user()->id,
            'type' => 'priority_change',
            'description' => Auth::user()->name.' alterou a prioridade',
        ]);

        return $task->refresh();
    }

    public function updateTaskDescription(int $taskId, ?string $description): Task
    {
        $task = Task::query()->findOrFail($taskId);

        $task->update([
            'description' => $description,
            'updated_at' => now(),
        ]);

        TaskActivity::create([
            'task_id' => $task->id,
            'user_id' => Auth::user()->id,
            'type' => 'description_change',
            'description' => Auth::user()->name.' alterou a descricao',
        ]);

        return $task->refresh();
    }

    public function updateTaskDeadline(int $taskId, ?string $deadlineAt): Task
    {
        $task = Task::query()->findOrFail($taskId);

        $task->update([
            'deadline_at' => $deadlineAt,
            'updated_at' => now(),
        ]);

        TaskActivity::create([
            'task_id' => $task->id,
            'user_id' => Auth::user()->id,
            'type' => 'deadline_change',
            'description' => Auth::user()->name.' alterou o prazo',
        ]);

        return $task->refresh();
    }

    public function updateStepOrganizationResponsible(int $stepId, ?int $organizationId): TaskStep
    {
        $step = TaskStep::query()->findOrFail($stepId);

        $step->update([
            'organization_id' => $organizationId,
            'user_id' => null,
            'updated_at' => now(),
        ]);

        TaskStepActivity::create([
            'task_step_id' => $step->id,
            'user_id' => Auth::user()->id,
            'type' => 'organization_responsable_change',
            'description' => Auth::user()->name.' alterou o responsavel',
        ]);

        return $step->refresh()->load(['task.taskHub', 'organization.users']);
    }

    public function updateStepResponsible(int $stepId, ?int $userId): TaskStep
    {
        $step = TaskStep::query()->findOrFail($stepId);

        $step->update([
            'user_id' => $userId,
            'updated_at' => now(),
        ]);

        TaskStepActivity::create([
            'task_step_id' => $step->id,
            'user_id' => Auth::user()->id,
            'type' => 'responsable_change',
            'description' => Auth::user()->name.' alterou o responsavel',
        ]);

        return $step->refresh();
    }

    public function updateStepCategory(int $stepId, ?int $categoryId): TaskStep
    {
        $step = TaskStep::query()->findOrFail($stepId);

        $step->update([
            'task_category_id' => $categoryId,
            'updated_at' => now(),
        ]);

        TaskStepActivity::create([
            'task_step_id' => $step->id,
            'user_id' => Auth::user()->id,
            'type' => 'category_change',
            'description' => Auth::user()->name.' alterou a categoria',
        ]);

        return $step->refresh();
    }

    public function updateStepPriority(int $stepId, ?int $priorityId): TaskStep
    {
        $step = TaskStep::query()->findOrFail($stepId);

        $step->update([
            'task_priority_id' => $priorityId,
            'updated_at' => now(),
        ]);

        TaskStepActivity::create([
            'task_step_id' => $step->id,
            'user_id' => Auth::user()->id,
            'type' => 'priority_change',
            'description' => Auth::user()->name.' alterou a prioridade',
        ]);

        return $step->refresh();
    }

    public function updateStepDescription(int $stepId, ?string $description): TaskStep
    {
        $step = TaskStep::query()->findOrFail($stepId);

        $step->update([
            'description' => $description,
            'updated_at' => now(),
        ]);

        return $step->refresh();
    }

    public function updateStepDeadline(int $stepId, ?string $deadlineAt): TaskStep
    {
        $step = TaskStep::query()->findOrFail($stepId);

        $step->update([
            'deadline_at' => $deadlineAt,
            'updated_at' => now(),
        ]);

        TaskStepActivity::create([
            'task_step_id' => $step->id,
            'user_id' => Auth::user()->id,
            'type' => 'deadline_change',
            'description' => Auth::user()->name.' alterou o prazo',
        ]);

        return $step->refresh();
    }

    /**
     * @return array{
     *     total:int,
     *     in_progress:int,
     *     completed:int,
     *     overdue:int,
     *     cancelled:int,
     *     overdue_tasks:array<int, array{id:int, code:string, title:string, deadline_at:string|null, responsible:string}>,
     *     overdue_steps:array<int, array{id:int, code:string, title:string, deadline_at:string|null, responsible:string, task_code:string|null}>,
     *     tasks_by_responsible:array<int, array{label:string, total:int}>,
     *     tasks_by_step_status:array<int, array{label:string, total:int}>,
     *     steps_by_organization:array<int, array{label:string, total:int}>,
     *     steps_by_responsible:array<int, array{label:string, total:int}>,
     *     tasks_by_status_active:array<int, array{label:string, total:int, color:string|null}>,
     *     steps_by_status_active:array<int, array{label:string, total:int, color:string|null}>,
     *     tasks_active_total:int,
     *     steps_active_total:int
     * }
     */
    public function dashboard(string $id): array
    {
        $taskHub = TaskHub::where('uuid', $id)->firstOrFail();

        $baseQuery = Task::query()->where('task_hub_id', $taskHub->id);

        $total = (clone $baseQuery)->count();

        $statusIds = TaskStatus::query()
            ->where('task_hub_id', $taskHub->id)
            ->get()
            ->reduce(function (array $carry, TaskStatus $status): array {
                if ($this->isInProgressStatusTitle($status->title) && $carry['in_progress'] === null) {
                    $carry['in_progress'] = (int) $status->id;
                }

                if ($this->isCompletedStatusTitle($status->title) && $carry['completed'] === null) {
                    $carry['completed'] = (int) $status->id;
                }

                if ($this->isCancelledStatusTitle($status->title) && $carry['cancelled'] === null) {
                    $carry['cancelled'] = (int) $status->id;
                }

                return $carry;
            }, ['in_progress' => null, 'completed' => null, 'cancelled' => null]);

        $inProgressStatusId = $statusIds['in_progress'];
        $completedStatusId = $statusIds['completed'];
        $cancelledStatusId = $statusIds['cancelled'];

        $inProgress = $inProgressStatusId
            ? (clone $baseQuery)->where('task_status_id', $inProgressStatusId)->count()
            : 0;
        $completed = $completedStatusId
            ? (clone $baseQuery)->where('task_status_id', $completedStatusId)->count()
            : 0;
        $cancelled = $cancelledStatusId
            ? (clone $baseQuery)->where('task_status_id', $cancelledStatusId)->count()
            : 0;

        $terminalStatusIds = array_values(array_filter([$completedStatusId, $cancelledStatusId]));
        $overdueQuery = (clone $baseQuery)
            ->whereNotNull('deadline_at')
            ->where('deadline_at', '<', now());

        if ($terminalStatusIds !== []) {
            $overdueQuery->whereNotIn('task_status_id', $terminalStatusIds);
        }

        $overdue = $overdueQuery->count();

        $overdueTasks = Task::query()
            ->where('task_hub_id', $taskHub->id)
            ->whereNotNull('deadline_at')
            ->where('deadline_at', '<', now())
            ->when($terminalStatusIds !== [], fn ($query) => $query->whereNotIn('task_status_id', $terminalStatusIds))
            ->with('user')
            ->orderBy('deadline_at')
            ->limit(8)
            ->get()
            ->map(function (Task $task) {
                return [
                    'id' => $task->id,
                    'code' => $task->code,
                    'title' => $task->title,
                    'deadline_at' => $task->deadline_at?->format('Y-m-d'),
                    'responsible' => $task->user?->name ?? 'Sem responsável',
                ];
            })
            ->all();

        $responsibleCounts = (clone $baseQuery)
            ->select('user_id', DB::raw('count(*) as total'))
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $userIds = $responsibleCounts
            ->pluck('user_id')
            ->filter()
            ->values();

        $users = User::query()
            ->whereIn('id', $userIds)
            ->pluck('name', 'id');

        $tasksByResponsible = $responsibleCounts
            ->map(function ($row) use ($users) {
                $label = $row->user_id
                    ? ($users[$row->user_id] ?? 'Usuário')
                    : 'Sem responsável';

                return [
                    'label' => $label,
                    'total' => (int) $row->total,
                ];
            })
            ->take(5)
            ->values()
            ->all();

        $stepStatusCounts = TaskStep::query()
            ->where('task_hub_id', $taskHub->id)
            ->select('task_status_id', DB::raw('count(*) as total'))
            ->groupBy('task_status_id')
            ->orderByDesc('total')
            ->get();

        $statusIds = $stepStatusCounts
            ->pluck('task_status_id')
            ->filter()
            ->values();

        $statuses = TaskStepStatus::query()
            ->whereIn('id', $statusIds)
            ->pluck('title', 'id');

        $tasksByStepStatus = $stepStatusCounts
            ->map(function ($row) use ($statuses) {
                $label = $row->task_status_id
                    ? ($statuses[$row->task_status_id] ?? 'Status')
                    : 'Sem status';

                return [
                    'label' => $label,
                    'total' => (int) $row->total,
                ];
            })
            ->values()
            ->all();

        $stepResponsibleCounts = TaskStep::query()
            ->where('task_hub_id', $taskHub->id)
            ->select('user_id', DB::raw('count(*) as total'))
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $stepUserIds = $stepResponsibleCounts
            ->pluck('user_id')
            ->filter()
            ->values();

        $stepUsers = User::query()
            ->whereIn('id', $stepUserIds)
            ->pluck('name', 'id');

        $stepsByResponsible = $stepResponsibleCounts
            ->map(function ($row) use ($stepUsers) {
                $label = $row->user_id
                    ? ($stepUsers[$row->user_id] ?? 'Usuário')
                    : 'Sem responsável';

                return [
                    'label' => $label,
                    'total' => (int) $row->total,
                ];
            })
            ->take(5)
            ->values()
            ->all();

        $stepOrganizationCounts = TaskStep::query()
            ->where('task_hub_id', $taskHub->id)
            ->select('organization_id', DB::raw('count(*) as total'))
            ->groupBy('organization_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $organizationIds = $stepOrganizationCounts
            ->pluck('organization_id')
            ->filter()
            ->values();

        $organizations = OrganizationChart::query()
            ->whereIn('id', $organizationIds)
            ->get(['id', 'acronym', 'title'])
            ->keyBy('id');

        $stepsByOrganization = $stepOrganizationCounts
            ->map(function ($row) use ($organizations) {
                $organization = $row->organization_id
                    ? $organizations->get($row->organization_id)
                    : null;

                $label = $organization
                    ? ($organization->acronym ?: $organization->title)
                    : 'Sem setor';

                return [
                    'label' => $label,
                    'total' => (int) $row->total,
                ];
            })
            ->take(5)
            ->values()
            ->all();

        $taskStatuses = TaskStatus::query()
            ->where('task_hub_id', $taskHub->id)
            ->orderBy('id')
            ->get(['id', 'title', 'color'])
            ->reject(fn (TaskStatus $status): bool => $this->isCompletedStatusTitle($status->title) || $this->isCancelledStatusTitle($status->title))
            ->values();

        $taskStatusCounts = Task::query()
            ->where('task_hub_id', $taskHub->id)
            ->whereIn('task_status_id', $taskStatuses->pluck('id'))
            ->select('task_status_id', DB::raw('count(*) as total'))
            ->groupBy('task_status_id')
            ->get()
            ->keyBy('task_status_id');

        $tasksByStatusActive = $taskStatuses
            ->map(function (TaskStatus $status) use ($taskStatusCounts) {
                return [
                    'label' => $status->title,
                    'total' => (int) ($taskStatusCounts[$status->id]->total ?? 0),
                    'color' => $status->color,
                ];
            })
            ->values()
            ->all();

        $stepStatuses = TaskStepStatus::query()
            ->where('task_hub_id', $taskHub->id)
            ->orderBy('id')
            ->get(['id', 'title', 'color'])
            ->reject(fn (TaskStepStatus $status): bool => $this->isCompletedStatusTitle($status->title) || $this->isCancelledStatusTitle($status->title))
            ->values();

        $stepStatusCountsActive = TaskStep::query()
            ->where('task_hub_id', $taskHub->id)
            ->whereIn('task_status_id', $stepStatuses->pluck('id'))
            ->select('task_status_id', DB::raw('count(*) as total'))
            ->groupBy('task_status_id')
            ->get()
            ->keyBy('task_status_id');

        $stepsByStatusActive = $stepStatuses
            ->map(function (TaskStepStatus $status) use ($stepStatusCountsActive) {
                return [
                    'label' => $status->title,
                    'total' => (int) ($stepStatusCountsActive[$status->id]->total ?? 0),
                    'color' => $status->color,
                ];
            })
            ->values()
            ->all();

        $stepTerminalStatusIds = TaskStepStatus::query()
            ->where('task_hub_id', $taskHub->id)
            ->get()
            ->filter(fn (TaskStepStatus $status): bool => $this->isCompletedStatusTitle($status->title) || $this->isCancelledStatusTitle($status->title))
            ->pluck('id')
            ->all();

        $overdueSteps = TaskStep::query()
            ->where('task_hub_id', $taskHub->id)
            ->whereNotNull('deadline_at')
            ->where('deadline_at', '<', now())
            ->when($stepTerminalStatusIds !== [], fn ($query) => $query->whereNotIn('task_status_id', $stepTerminalStatusIds))
            ->with(['user', 'task'])
            ->orderBy('deadline_at')
            ->limit(8)
            ->get()
            ->map(function (TaskStep $step) {
                return [
                    'id' => $step->id,
                    'code' => $step->code,
                    'title' => $step->title,
                    'deadline_at' => $step->deadline_at?->format('Y-m-d'),
                    'responsible' => $step->user?->name ?? 'Sem responsável',
                    'task_code' => $step->task?->code,
                ];
            })
            ->all();

        $tasksActiveTotal = collect($tasksByStatusActive)->sum('total');
        $stepsActiveTotal = collect($stepsByStatusActive)->sum('total');

        return [
            'total' => $total,
            'in_progress' => $inProgress,
            'completed' => $completed,
            'overdue' => $overdue,
            'cancelled' => $cancelled,
            'overdue_tasks' => $overdueTasks,
            'overdue_steps' => $overdueSteps,
            'tasks_by_responsible' => $tasksByResponsible,
            'tasks_by_step_status' => $tasksByStepStatus,
            'steps_by_organization' => $stepsByOrganization,
            'steps_by_responsible' => $stepsByResponsible,
            'tasks_by_status_active' => $tasksByStatusActive,
            'steps_by_status_active' => $stepsByStatusActive,
            'tasks_active_total' => $tasksActiveTotal,
            'steps_active_total' => $stepsActiveTotal,
        ];
    }

    /**
     * @return array{
     *     hubs_total:int,
     *     total:int,
     *     overdue:int,
     *     statuses:array<int, array{label:string, total:int, color:string}>,
     *     users:array<int, array{label:string, total:int}>,
     *     organizations:array<int, array{label:string, total:int}>,
     *     overdue_tasks:array<int, array{id:int, code:string, title:string, deadline_at:string|null, responsible:string, hub:string}>
     * }
     */
    public function userOverview(int $userId): array
    {
        $hubIds = $this->accessibleHubIds($userId);

        if ($hubIds === []) {
            return [
                'hubs_total' => 0,
                'total' => 0,
                'overdue' => 0,
                'statuses' => [],
            ];
        }

        $baseTaskQuery = Task::query()->whereIn('tasks.task_hub_id', $hubIds);
        $total = (clone $baseTaskQuery)->count();

        $statusRows = (clone $baseTaskQuery)
            ->leftJoin('task_statuses', 'task_statuses.id', '=', 'tasks.task_status_id')
            ->selectRaw('task_statuses.title as title, task_statuses.color as color, count(tasks.id) as total')
            ->whereNotNull('task_statuses.id')
            ->groupBy('task_statuses.title', 'task_statuses.color')
            ->orderBy('task_statuses.title')
            ->get();

        $statuses = $statusRows
            ->map(fn ($row) => [
                'label' => $row->title,
                'total' => (int) $row->total,
                'color' => $this->colorToHex($row->color),
            ])
            ->values();

        $tasksWithoutStatus = (clone $baseTaskQuery)
            ->whereNull('task_status_id')
            ->count();

        if ($tasksWithoutStatus > 0) {
            $statuses->push([
                'label' => 'Sem status',
                'total' => $tasksWithoutStatus,
                'color' => $this->colorToHex(null),
            ]);
        }

        $overdueQuery = (clone $baseTaskQuery)
            ->whereNotNull('deadline_at')
            ->where('deadline_at', '<', now());

        $overdueQuery->where(function ($query): void {
            $query->whereNull('task_status_id')
                ->orWhereHas('taskStatus', fn ($statusQuery) => $statusQuery->whereNotIn('id', $this->terminalStatusIdsForQuery()));
        });

        $overdue = $overdueQuery->count();

        return [
            'hubs_total' => count($hubIds),
            'total' => $total,
            'overdue' => $overdue,
            'statuses' => $statuses->all(),
        ];
    }

    /**
     * @return array<int, array{
     *     status_id:int,
     *     title:string,
     *     color_code_tailwind:string|null,
     *     tasks:\Illuminate\Support\Collection<int, \App\Models\Task\Task>
     * }>
     */
    public function kanban(string $id): array
    {
        $taskHub = TaskHub::where('uuid', $id)->firstOrFail();

        $tasks = Task::query()
            ->where('task_hub_id', $taskHub->id)
            ->where(function ($query): void {
                $query->whereNull('finished_at')
                    ->orWhere('finished_at', '>=', now()->subDays(3));
            })
            ->with([
                'user',
                'taskCategory',
                'taskPriority',
                'taskStatus',
            ])
            ->orderBy('kanban_order')
            ->orderBy('id')
            ->get();

        $tasksByStatus = $tasks->groupBy('task_status_id');

        $statuses = TaskStatus::query()
            ->where('task_hub_id', $taskHub->id)
            ->orderBy('id')
            ->get()
            ->sortBy(function (TaskStatus $status): int {
                if ($this->isCancelledStatusTitle($status->title)) {
                    return 2;
                }

                if ($this->isCompletedStatusTitle($status->title)) {
                    return 1;
                }

                return 0;
            })
            ->values();

        $columns = collect();

        if ($tasksByStatus->has(null)) {
            $columns->push([
                'status_id' => 0,
                'title' => 'Sem status',
                'color' => null,
                'color_code_tailwind' => 'bg-gray-100 text-gray-700',
                'tasks' => $tasksByStatus->get(null, collect()),
            ]);
        }

        foreach ($statuses as $status) {
            $columns->push([
                'status_id' => $status->id,
                'title' => $status->title,
                'color' => $status->color,
                'color_code_tailwind' => $status->color_code_tailwind,
                'tasks' => $tasksByStatus->get($status->id, collect()),
            ]);
        }

        return $columns->all();
    }

    public function moveKanbanTask(
        string $hubUuid,
        int $taskId,
        ?int $fromStatusId,
        ?int $toStatusId,
        array $sourceOrder,
        array $targetOrder,
        ?string $reason = null,
        ?string $reasonType = null
    ): bool {
        $taskHub = TaskHub::where('uuid', $hubUuid)->firstOrFail();

        return DB::transaction(function () use ($taskHub, $taskId, $fromStatusId, $toStatusId, $sourceOrder, $targetOrder, $reason, $reasonType): bool {
            $task = Task::query()
                ->where('task_hub_id', $taskHub->id)
                ->lockForUpdate()
                ->findOrFail($taskId);

            $fromStatusId = $fromStatusId === 0 ? null : $fromStatusId;
            $toStatusId = $toStatusId === 0 ? null : $toStatusId;
            $reason = $reason !== null ? trim($reason) : null;

            if ($this->isInvalidTaskTerminalSwap($taskHub->id, $fromStatusId, $toStatusId)) {
                return false;
            }

            $expectedReasonType = $this->taskReasonTypeForTransition($taskHub->id, $fromStatusId, $toStatusId);
            if ($expectedReasonType !== null && ($reason === null || $reason === '')) {
                return false;
            }

            if ($reasonType !== null && $expectedReasonType !== null && $reasonType !== $expectedReasonType) {
                return false;
            }

            if ($expectedReasonType !== null) {
                $reasonType = $expectedReasonType;
            }

            if ($reasonType === 'completion' && ! $this->canMarkTaskAsCompleted($task)) {
                return false;
            }

            $previousStatusId = $task->task_status_id;

            if ($fromStatusId !== $toStatusId) {
                $this->applyStatusUpdate($task, $toStatusId);
            }

            if ($fromStatusId === $toStatusId) {
                $this->applyKanbanOrder($taskHub->id, $toStatusId, $targetOrder);
            } else {
                $this->applyKanbanOrder($taskHub->id, $fromStatusId, $sourceOrder);
                $this->applyKanbanOrder($taskHub->id, $toStatusId, $targetOrder);
            }

            $description = ($this->actorName()).' moveu a tarefa no kanban';
            if ($reason && $reasonType === 'completion') {
                $description = ($this->actorName()).' concluiu a tarefa: '.$reason;
            }
            if ($reason && $reasonType === 'cancellation') {
                $description = ($this->actorName()).' cancelou a tarefa: '.$reason;
            }
            if ($reason && $reasonType === 'reopen') {
                $description = ($this->actorName()).' reabriu a tarefa: '.$reason;
            }

            $this->recordActivity(
                $task,
                'kanban_move',
                $description,
                [
                    'from_status_id' => $previousStatusId,
                    'to_status_id' => $toStatusId,
                    'source_order' => $sourceOrder,
                    'target_order' => $targetOrder,
                    'reason' => $reason,
                    'reason_type' => $reasonType,
                ]
            );

            return true;
        });
    }

    /**
     * @return array<int, array{
     *     status_id:int,
     *     title:string,
     *     color:string|null,
     *     color_code_tailwind:string|null,
     *     steps:\Illuminate\Support\Collection<int, \App\Models\Task\TaskStep>
     * }>
     */
    public function stepKanban(string $id, array $filters = []): array
    {
        $taskHub = TaskHub::where('uuid', $id)->firstOrFail();

        $taskId = isset($filters['task_id']) && $filters['task_id'] !== ''
            ? (int) $filters['task_id']
            : null;
        $organizationId = isset($filters['organization_id']) && $filters['organization_id'] !== ''
            ? (int) $filters['organization_id']
            : null;
        $userId = isset($filters['user_id']) && $filters['user_id'] !== ''
            ? (int) $filters['user_id']
            : null;

        $statuses = TaskStepStatus::query()
            ->where('task_hub_id', $taskHub->id)
            ->orderBy('id')
            ->get()
            ->sortBy(function (TaskStepStatus $status): int {
                if ($this->isCancelledStatusTitle($status->title)) {
                    return 2;
                }

                if ($this->isCompletedStatusTitle($status->title)) {
                    return 1;
                }

                return 0;
            })
            ->values();

        $stepsQuery = TaskStep::query()
            ->where('task_hub_id', $taskHub->id)
            ->where(function ($query): void {
                $query->whereNull('finished_at')
                    ->orWhere('finished_at', '>=', now()->subDays(3));
            });

        if ($taskId !== null) {
            $stepsQuery->where('task_id', $taskId);
        }

        if ($organizationId !== null) {
            $stepsQuery->where('organization_id', $organizationId);
        }

        if ($userId !== null) {
            $stepsQuery->where('user_id', $userId);
        }

        $steps = $stepsQuery
            ->with([
                'task',
                'user',
                'organization',
                'taskPriority',
                'taskStepStatus',
            ])
            ->orderBy('kanban_order')
            ->orderBy('id')
            ->get();

        $stepsByStatus = $steps->groupBy('task_status_id');

        $columns = collect();

        if ($stepsByStatus->has(null)) {
            $columns->push([
                'status_id' => 0,
                'title' => 'Sem status',
                'color' => null,
                'color_code_tailwind' => 'bg-gray-100 text-gray-700',
                'steps' => $stepsByStatus->get(null, collect()),
            ]);
        }

        foreach ($statuses as $status) {
            $columns->push([
                'status_id' => $status->id,
                'title' => $status->title,
                'color' => $status->color,
                'color_code_tailwind' => $status->color_code_tailwind,
                'steps' => $stepsByStatus->get($status->id, collect()),
            ]);
        }

        return $columns->all();
    }

    public function moveKanbanStep(
        string $hubUuid,
        int $stepId,
        ?int $fromStatusId,
        ?int $toStatusId,
        array $sourceOrder,
        array $targetOrder,
        ?string $reason = null,
        ?string $reasonType = null
    ): bool {
        $taskHub = TaskHub::where('uuid', $hubUuid)->firstOrFail();

        return DB::transaction(function () use ($taskHub, $stepId, $fromStatusId, $toStatusId, $sourceOrder, $targetOrder, $reason, $reasonType): bool {
            $step = TaskStep::query()
                ->where('task_hub_id', $taskHub->id)
                ->lockForUpdate()
                ->findOrFail($stepId);

            $fromStatusId = $fromStatusId === 0 ? null : $fromStatusId;
            $toStatusId = $toStatusId === 0 ? null : $toStatusId;
            $reason = $reason !== null ? trim($reason) : null;

            $previousStatusId = $step->task_status_id;

            if ($this->isInvalidStepTerminalSwap($taskHub->id, $fromStatusId, $toStatusId)) {
                return false;
            }

            $expectedReasonType = $this->stepReasonTypeForTransition($taskHub->id, $fromStatusId, $toStatusId);
            if ($expectedReasonType !== null && ($reason === null || $reason === '')) {
                return false;
            }

            if ($reasonType !== null && $expectedReasonType !== null && $reasonType !== $expectedReasonType) {
                return false;
            }

            if ($expectedReasonType !== null) {
                $reasonType = $expectedReasonType;
            }

            if ($this->startingWorkflowStepIsBlocked($step, $toStatusId)) {
                return false;
            }

            if ($fromStatusId !== $toStatusId) {
                $this->applyStepStatusUpdate($step, $toStatusId);
            }

            if ($fromStatusId === $toStatusId) {
                $this->applyKanbanStepOrder($taskHub->id, $toStatusId, $targetOrder);
            } else {
                $this->applyKanbanStepOrder($taskHub->id, $fromStatusId, $sourceOrder);
                $this->applyKanbanStepOrder($taskHub->id, $toStatusId, $targetOrder);
            }

            $toStatusLabel = $toStatusId
                ? (TaskStepStatus::query()
                    ->where('task_hub_id', $taskHub->id)
                    ->find($toStatusId)?->title ?? 'Status')
                : 'Sem status';

            $description = ($this->actorName()).' moveu a etapa no kanban para '.$toStatusLabel;
            if ($reason && $reasonType === 'completion') {
                $description = ($this->actorName()).' concluiu a etapa: '.$reason;
                $this->recordTaskCommentForCompletedStep($step, $reason);
                $this->recordStepComment($step, $reason);
                $this->recordTaskStepCompletionActivity($step);
            }
            if ($reason && $reasonType === 'cancellation') {
                $description = ($this->actorName()).' cancelou a etapa: '.$reason;
                $this->recordTaskCommentForCancelledStep($step, $reason);
                $this->recordStepComment($step, $reason);
                $this->recordTaskStepCancellationActivity($step);
            }
            if ($reason && $reasonType === 'reopen') {
                $description = ($this->actorName()).' reabriu a etapa: '.$reason;
                $this->recordTaskCommentForReopenedStep($step, $reason);
                $this->recordStepComment($step, $reason);
                $this->recordTaskStepReopenActivity($step);
            }

            TaskStepActivity::create([
                'task_step_id' => $step->id,
                'user_id' => Auth::user()?->id,
                'type' => 'kanban_move',
                'description' => $description,
                'meta' => [
                    'from_status_id' => $previousStatusId,
                    'to_status_id' => $toStatusId,
                    'source_order' => $sourceOrder,
                    'target_order' => $targetOrder,
                    'reason' => $reason,
                    'reason_type' => $reasonType,
                ],
            ]);
            return true;
        });
    }

    public function completeStep(int $stepId, string $comment): TaskStep
    {
        $step = TaskStep::query()->findOrFail($stepId);
        $completedStatusId = TaskStepStatus::query()
            ->where('task_hub_id', $step->task_hub_id)
            ->get()
            ->first(fn (TaskStepStatus $status): bool => $this->isCompletedStatusTitle($status->title))
            ?->id;

        if (! $completedStatusId) {
            return $step;
        }

        $step->update([
            'task_status_id' => $completedStatusId,
            'finished_at' => now(),
        ]);

        $this->recordTaskCommentForCompletedStep($step, $comment);
        $this->recordTaskStepCompletionActivity($step);

        TaskStepActivity::create([
            'task_step_id' => $step->id,
            'user_id' => Auth::user()?->id,
            'type' => 'comment',
            'description' => $comment,
        ]);

        TaskStepActivity::create([
            'task_step_id' => $step->id,
            'user_id' => Auth::user()?->id,
            'type' => 'finished_change',
            'description' => $this->actorName().' marcou a etapa como concluída',
        ]);

        return $step->refresh();
    }

    public function changeStepStatus(int $stepId, ?int $statusId, ?string $description = null, string $type = 'status_change'): ?TaskStep
    {
        $step = TaskStep::query()->findOrFail($stepId);

        if ($this->startingWorkflowStepIsBlocked($step, $statusId)) {
            return null;
        }

        $previousStatusId = $step->task_status_id;

        $this->applyStepStatusUpdate($step, $statusId);

        TaskStepActivity::create([
            'task_step_id' => $step->id,
            'user_id' => Auth::user()?->id,
            'type' => $type,
            'description' => $description ?? ($this->actorName().' alterou o status'),
            'meta' => [
                'from_status_id' => $previousStatusId,
                'to_status_id' => $statusId,
            ],
        ]);

        return $step->refresh();
    }

    public function storeStepComment(int $stepId, string $comment): void
    {
        $step = TaskStep::query()->findOrFail($stepId);

        TaskStepActivity::create([
            'task_step_id' => $stepId,
            'user_id' => Auth::user()?->id,
            'type' => 'comment',
            'description' => $comment,
        ]);

        Task::query()
            ->findOrFail($step->task_id)
            ->update([
                'updated_at' => now(),
            ]);
    }

    public function changeStatus(int $taskId, ?int $statusId, ?string $description = null, string $type = 'status_change'): Task
    {
        $task = Task::findOrFail($taskId);
        $previousStatusId = $task->task_status_id;
        $reasonType = $this->taskReasonTypeForTransition($task->task_hub_id, $previousStatusId, $statusId);

        if ($reasonType === 'completion' && ! $this->canMarkTaskAsCompleted($task)) {
            return $task->refresh();
        }

        $this->applyStatusUpdate($task, $statusId);

        $this->recordActivity(
            $task,
            $type,
            $description ?? ($this->actorName().' alterou o status'),
            [
                'from_status_id' => $previousStatusId,
                'to_status_id' => $statusId,
            ]
        );

        return $task->refresh();
    }

    private function actorName(): string
    {
        return Auth::user()?->name ?? 'Sistema';
    }

    private function recordActivity(Task $task, string $type, string $description, ?array $meta = null): void
    {
        TaskActivity::create([
            'task_id' => $task->id,
            'user_id' => Auth::user()?->id,
            'type' => $type,
            'description' => $description,
            'meta' => $meta,
        ]);
    }

    private function recordTaskCommentForCompletedStep(TaskStep $step, string $comment): void
    {
        TaskActivity::create([
            'task_id' => $step->task_id,
            'user_id' => Auth::user()?->id,
            'type' => 'comment',
            'description' => $comment,
        ]);
    }

    private function recordTaskStepCompletionActivity(TaskStep $step): void
    {
        TaskActivity::create([
            'task_id' => $step->task_id,
            'user_id' => Auth::user()?->id,
            'type' => 'step_finished_change',
            'description' => $this->actorName().' concluiu a etapa '.$step->title,
        ]);
    }

    private function recordTaskCommentForCancelledStep(TaskStep $step, string $reason): void
    {
        TaskActivity::create([
            'task_id' => $step->task_id,
            'user_id' => Auth::user()?->id,
            'type' => 'comment',
            'description' => $reason,
        ]);
    }

    private function recordTaskStepCancellationActivity(TaskStep $step): void
    {
        TaskActivity::create([
            'task_id' => $step->task_id,
            'user_id' => Auth::user()?->id,
            'type' => 'step_cancellation_change',
            'description' => $this->actorName().' cancelou a etapa '.$step->title,
        ]);
    }

    private function recordTaskCommentForReopenedStep(TaskStep $step, string $reason): void
    {
        TaskActivity::create([
            'task_id' => $step->task_id,
            'user_id' => Auth::user()?->id,
            'type' => 'comment',
            'description' => $reason,
        ]);
    }

    private function recordStepComment(TaskStep $step, string $reason): void
    {
        TaskStepActivity::create([
            'task_step_id' => $step->id,
            'user_id' => Auth::user()?->id,
            'type' => 'comment',
            'description' => $reason,
        ]);
    }

    private function recordTaskStepReopenActivity(TaskStep $step): void
    {
        TaskActivity::create([
            'task_id' => $step->task_id,
            'user_id' => Auth::user()?->id,
            'type' => 'step_reopen_change',
            'description' => $this->actorName().' reabriu a etapa '.$step->title,
        ]);
    }

    private function applyStepStatusUpdate(TaskStep $step, ?int $statusId): void
    {
        $updates = [
            'task_status_id' => $statusId,
        ];

        if ($statusId !== null && in_array($statusId, $this->stepInProgressStatusIds($step->task_hub_id), true) && $step->started_at === null) {
            $updates['started_at'] = now();
        }

        $terminalStatusIds = $this->stepTerminalStatusIds($step->task_hub_id);

        if ($statusId !== null && in_array($statusId, $terminalStatusIds, true) && $step->finished_at === null) {
            $updates['finished_at'] = now();
        }

        if (($statusId === null || ! in_array($statusId, $terminalStatusIds, true)) && $step->finished_at !== null) {
            $updates['finished_at'] = null;
        }

        $step->update($updates);
    }

    private function startingWorkflowStepIsBlocked(TaskStep $step, ?int $statusId): bool
    {
        if ($statusId === null || ! in_array($statusId, $this->stepInProgressStatusIds($step->task_hub_id), true)) {
            return false;
        }

        if ($step->workflow_step_order === null || $step->workflow_step_order < 2) {
            return false;
        }

        $previousStep = TaskStep::query()
            ->where('task_id', $step->task_id)
            ->whereNotNull('workflow_step_order')
            ->where('workflow_step_order', '<', $step->workflow_step_order)
            ->orderByDesc('workflow_step_order')
            ->first();

        if (! $previousStep) {
            return false;
        }

        if (! $previousStep->is_required || $previousStep->allow_parallel) {
            return false;
        }

        if ($previousStep->finished_at !== null) {
            return false;
        }

        $previousStatusId = $previousStep->task_status_id;

        if ($previousStatusId !== null && in_array((int) $previousStatusId, $this->stepTerminalStatusIds($step->task_hub_id), true)) {
            return false;
        }

        return true;
    }

    private function applyStatusUpdate(Task $task, ?int $statusId): void
    {
        $updates = [
            'task_status_id' => $statusId,
        ];

        if ($statusId !== null && in_array($statusId, $this->taskInProgressStatusIds($task->task_hub_id), true) && $task->started_at === null) {
            $updates['started_at'] = now();
        }

        $terminalStatusIds = $this->terminalStatusIds($task->task_hub_id);

        if ($statusId !== null && in_array($statusId, $terminalStatusIds, true) && $task->finished_at === null) {
            $updates['finished_at'] = now();
        }

        if (($statusId === null || ! in_array($statusId, $terminalStatusIds, true)) && $task->finished_at !== null) {
            $updates['finished_at'] = null;
        }

        $task->update($updates);
    }

    /**
     * @return array<int>
     */
    private function terminalStatusIds(int $taskHubId): array
    {
        return TaskStatus::query()
            ->where('task_hub_id', $taskHubId)
            ->get()
            ->filter(fn (TaskStatus $status): bool => $this->isCompletedStatusTitle($status->title) || $this->isCancelledStatusTitle($status->title))
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    /**
     * @return array<int>
     */
    private function stepTerminalStatusIds(int $taskHubId): array
    {
        return TaskStepStatus::query()
            ->where('task_hub_id', $taskHubId)
            ->get()
            ->filter(fn (TaskStepStatus $status): bool => $this->isCompletedStatusTitle($status->title) || $this->isCancelledStatusTitle($status->title))
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    /**
     * @return array<int>
     */
    private function stepInProgressStatusIds(int $taskHubId): array
    {
        return TaskStepStatus::query()
            ->where('task_hub_id', $taskHubId)
            ->get()
            ->filter(fn (TaskStepStatus $status): bool => $this->isInProgressStatusTitle($status->title))
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    /**
     * @return array<int>
     */
    private function taskInProgressStatusIds(int $taskHubId): array
    {
        return TaskStatus::query()
            ->where('task_hub_id', $taskHubId)
            ->get()
            ->filter(fn (TaskStatus $status): bool => $this->isInProgressStatusTitle($status->title))
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    /**
     * @return array<int>
     */
    private function terminalStatusIdsForQuery(): array
    {
        return TaskStatus::query()
            ->get(['id', 'title'])
            ->filter(fn (TaskStatus $status): bool => $this->isCompletedStatusTitle($status->title) || $this->isCancelledStatusTitle($status->title))
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();
    }

    /**
     * @return array<int>
     */
    private function accessibleHubIds(int $userId): array
    {
        return TaskHub::query()
            ->where(function ($query) use ($userId): void {
                $query->where('owner_id', $userId)
                    ->orWhereHas('members', function ($memberQuery) use ($userId): void {
                        $memberQuery->where('user_id', $userId);
                    })
                    ->orWhereHas('organizations.users', function ($organizationQuery) use ($userId): void {
                        $organizationQuery->where('users.id', $userId);
                    });
            })
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    private function collectAccessUsers(TaskHub $taskHub): Collection
    {
        $users = collect();

        if ($taskHub->owner) {
            $users->push($taskHub->owner);
        }

        foreach ($taskHub->members as $member) {
            if ($member->user) {
                $users->push($member->user);
            }
        }

        foreach ($taskHub->organizations as $organization) {
            foreach ($organization->users as $user) {
                $users->push($user);
            }
        }

        return $users
            ->unique('id')
            ->sortBy(fn (User $user) => $user->name ?? '')
            ->values();
    }

    /**
     * @return \Illuminate\Support\Collection<int, array{
     *     user:\App\Models\Administration\User\User,
     *     type:string,
     *     membership_id:int|null,
     *     sector_labels:array<int, string>,
     *     has_sector_access:bool
     * }>
     */
    private function collectAccessUserEntries(TaskHub $taskHub): Collection
    {
        $sectorMap = [];

        foreach ($taskHub->organizations as $organization) {
            $label = $organization->acronym ?: $organization->title;

            foreach ($organization->users as $user) {
                if (! $user) {
                    continue;
                }

                $sectorMap[$user->id] ??= [];
                if (! in_array($label, $sectorMap[$user->id], true)) {
                    $sectorMap[$user->id][] = $label;
                }
            }
        }

        $memberMap = [];
        foreach ($taskHub->members as $member) {
            if ($member->user) {
                $memberMap[$member->user->id] = $member->id;
            }
        }

        $users = $this->collectAccessUsers($taskHub);

        return $users
            ->map(function (User $user) use ($taskHub, $memberMap, $sectorMap): array {
                $type = 'sector';
                if ($taskHub->owner && $user->id === $taskHub->owner->id) {
                    $type = 'owner';
                } elseif (array_key_exists($user->id, $memberMap)) {
                    $type = 'member';
                }

                $sectorLabels = $sectorMap[$user->id] ?? [];

                return [
                    'user' => $user,
                    'type' => $type,
                    'membership_id' => $memberMap[$user->id] ?? null,
                    'sector_labels' => $sectorLabels,
                    'has_sector_access' => $sectorLabels !== [],
                ];
            })
            ->sortBy(fn (array $entry) => $entry['user']->name ?? '')
            ->values();
    }

    public function stepReasonTypeForTransition(int $taskHubId, ?int $fromStatusId, ?int $toStatusId): ?string
    {
        if ($fromStatusId === $toStatusId) {
            return null;
        }

        $toStatus = $toStatusId ? TaskStepStatus::query()->where('task_hub_id', $taskHubId)->find($toStatusId) : null;
        $fromStatus = $fromStatusId ? TaskStepStatus::query()->where('task_hub_id', $taskHubId)->find($fromStatusId) : null;

        if ($toStatus && $this->isCompletedStatusTitle($toStatus->title)) {
            return 'completion';
        }

        if ($toStatus && $this->isCancelledStatusTitle($toStatus->title)) {
            return 'cancellation';
        }

        if (
            $fromStatus
            && ($this->isCompletedStatusTitle($fromStatus->title) || $this->isCancelledStatusTitle($fromStatus->title))
            && ($toStatus === null || (! $this->isCompletedStatusTitle($toStatus->title) && ! $this->isCancelledStatusTitle($toStatus->title)))
        ) {
            return 'reopen';
        }

        return null;
    }

    public function taskReasonTypeForTransition(int $taskHubId, ?int $fromStatusId, ?int $toStatusId): ?string
    {
        if ($fromStatusId === $toStatusId) {
            return null;
        }

        $toStatus = $toStatusId ? TaskStatus::query()->where('task_hub_id', $taskHubId)->find($toStatusId) : null;
        $fromStatus = $fromStatusId ? TaskStatus::query()->where('task_hub_id', $taskHubId)->find($fromStatusId) : null;

        if ($toStatus && $this->isCompletedStatusTitle($toStatus->title)) {
            return 'completion';
        }

        if ($toStatus && $this->isCancelledStatusTitle($toStatus->title)) {
            return 'cancellation';
        }

        if (
            $fromStatus
            && ($this->isCompletedStatusTitle($fromStatus->title) || $this->isCancelledStatusTitle($fromStatus->title))
            && ($toStatus === null || (! $this->isCompletedStatusTitle($toStatus->title) && ! $this->isCancelledStatusTitle($toStatus->title)))
        ) {
            return 'reopen';
        }

        return null;
    }

    public function isInvalidStepTerminalSwap(int $taskHubId, ?int $fromStatusId, ?int $toStatusId): bool
    {
        if ($fromStatusId === $toStatusId || $fromStatusId === null || $toStatusId === null) {
            return false;
        }

        $fromStatus = TaskStepStatus::query()->where('task_hub_id', $taskHubId)->find($fromStatusId);
        $toStatus = TaskStepStatus::query()->where('task_hub_id', $taskHubId)->find($toStatusId);

        if (! $fromStatus || ! $toStatus) {
            return false;
        }

        return ($this->isCompletedStatusTitle($fromStatus->title) && $this->isCancelledStatusTitle($toStatus->title))
            || ($this->isCancelledStatusTitle($fromStatus->title) && $this->isCompletedStatusTitle($toStatus->title));
    }

    public function isInvalidTaskTerminalSwap(int $taskHubId, ?int $fromStatusId, ?int $toStatusId): bool
    {
        if ($fromStatusId === $toStatusId || $fromStatusId === null || $toStatusId === null) {
            return false;
        }

        $fromStatus = TaskStatus::query()->where('task_hub_id', $taskHubId)->find($fromStatusId);
        $toStatus = TaskStatus::query()->where('task_hub_id', $taskHubId)->find($toStatusId);

        if (! $fromStatus || ! $toStatus) {
            return false;
        }

        return ($this->isCompletedStatusTitle($fromStatus->title) && $this->isCancelledStatusTitle($toStatus->title))
            || ($this->isCancelledStatusTitle($fromStatus->title) && $this->isCompletedStatusTitle($toStatus->title));
    }

    private function normalizeStatusTitle(?string $title): string
    {
        return (string) Str::of((string) $title)
            ->lower()
            ->ascii()
            ->replaceMatches('/[^a-z0-9]+/u', ' ')
            ->squish();
    }

    private function isCompletedStatusTitle(?string $title): bool
    {
        $normalized = $this->normalizeStatusTitle($title);

        return str_contains($normalized, 'conclu') || str_contains($normalized, 'finaliz');
    }

    private function isCancelledStatusTitle(?string $title): bool
    {
        return str_contains($this->normalizeStatusTitle($title), 'cancel');
    }

    private function isInProgressStatusTitle(?string $title): bool
    {
        $normalized = $this->normalizeStatusTitle($title);

        return str_contains($normalized, 'andamento')
            || str_contains($normalized, 'execucao')
            || str_contains($normalized, 'execu');
    }

    private function canMarkTaskAsCompleted(Task $task): bool
    {
        $totalSteps = TaskStep::query()
            ->where('task_id', $task->id)
            ->count();

        if ($totalSteps === 0) {
            return true;
        }

        $completionStatusIds = $this->taskStepCompletionStatusIds($task->task_hub_id);

        if ($completionStatusIds === []) {
            return false;
        }

        $openOrNonCompletedSteps = TaskStep::query()
            ->where('task_id', $task->id)
            ->where(function ($query) use ($completionStatusIds): void {
                $query->whereNull('task_status_id')
                    ->orWhereNotIn('task_status_id', $completionStatusIds);
            })
            ->count();

        return $openOrNonCompletedSteps === 0;
    }

    /**
     * @return array<int>
     */
    private function taskStepCompletionStatusIds(int $taskHubId): array
    {
        return TaskStepStatus::query()
            ->where('task_hub_id', $taskHubId)
            ->get(['id', 'title'])
            ->filter(fn (TaskStepStatus $status): bool => $this->isCompletedStatusTitle($status->title))
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();
    }

    private function createDefaultStatusesForHub(int $taskHubId): void
    {
        $taskStatuses = [
            [
                'title' => 'Rascunho',
                'color' => 'gray',
                'color_code_tailwind' => 'bg-gray-100 text-gray-800 hover:bg-gray-200',
                'is_default' => true,
                'is_active' => true,
            ],
            [
                'title' => 'Em andamento',
                'color' => 'blue',
                'color_code_tailwind' => 'bg-blue-100 text-blue-700 hover:bg-blue-200',
                'is_default' => false,
                'is_active' => true,
            ],
            [
                'title' => 'Concluído',
                'color' => 'green',
                'color_code_tailwind' => 'bg-green-100 text-green-700 hover:bg-green-200',
                'is_default' => false,
                'is_active' => true,
            ],
            [
                'title' => 'Cancelado',
                'color' => 'red',
                'color_code_tailwind' => 'bg-red-100 text-red-700 hover:bg-red-200',
                'is_default' => false,
                'is_active' => true,
            ],
        ];

        foreach ($taskStatuses as $status) {
            TaskStatus::query()->firstOrCreate(
                ['task_hub_id' => $taskHubId, 'title' => $status['title']],
                $status
            );
        }

        $taskStepStatuses = [
            [
                'title' => 'Pendente',
                'color' => 'gray',
                'color_code_tailwind' => 'bg-gray-100 text-gray-700 hover:bg-gray-200',
                'is_default' => true,
                'is_active' => true,
            ],
            [
                'title' => 'Em andamento',
                'color' => 'blue',
                'color_code_tailwind' => 'bg-blue-100 text-blue-700 hover:bg-blue-200',
                'is_default' => false,
                'is_active' => true,
            ],
            [
                'title' => 'Concluída',
                'color' => 'green',
                'color_code_tailwind' => 'bg-green-100 text-green-700 hover:bg-green-200',
                'is_default' => false,
                'is_active' => true,
            ],
            [
                'title' => 'Cancelada',
                'color' => 'red',
                'color_code_tailwind' => 'bg-red-100 text-red-700 hover:bg-red-200',
                'is_default' => false,
                'is_active' => true,
            ],
        ];

        foreach ($taskStepStatuses as $status) {
            TaskStepStatus::query()->firstOrCreate(
                ['task_hub_id' => $taskHubId, 'title' => $status['title']],
                $status
            );
        }
    }

    private function colorToHex(?string $color): string
    {
        return match ($color) {
            'blue' => '#2563eb',
            'yellow' => '#ca8a04',
            'green' => '#15803d',
            'red' => '#dc2626',
            'emerald' => '#047857',
            'gray', null => '#6b7280',
            default => '#334155',
        };
    }

    private function nextKanbanOrder(int $taskHubId, ?int $statusId): int
    {
        $query = Task::query()->where('task_hub_id', $taskHubId);

        if ($statusId === null) {
            $query->whereNull('task_status_id');
        } else {
            $query->where('task_status_id', $statusId);
        }

        return ((int) $query->max('kanban_order')) + 1;
    }

    private function applyKanbanOrder(int $taskHubId, ?int $statusId, array $orderedIds): void
    {
        if ($orderedIds === []) {
            return;
        }

        foreach ($orderedIds as $index => $taskId) {
            $query = Task::query()
                ->where('task_hub_id', $taskHubId)
                ->whereKey($taskId);

            if ($statusId === null) {
                $query->whereNull('task_status_id');
            } else {
                $query->where('task_status_id', $statusId);
            }

            $query->update(['kanban_order' => $index + 1]);
        }
    }

    public function nextStepKanbanOrder(int $taskHubId, ?int $statusId): int
    {
        $query = TaskStep::query()->where('task_hub_id', $taskHubId);

        if ($statusId === null) {
            $query->whereNull('task_status_id');
        } else {
            $query->where('task_status_id', $statusId);
        }

        return ((int) $query->max('kanban_order')) + 1;
    }

    private function applyKanbanStepOrder(int $taskHubId, ?int $statusId, array $orderedIds): void
    {
        if ($orderedIds === []) {
            return;
        }

        foreach ($orderedIds as $index => $stepId) {
            $query = TaskStep::query()
                ->where('task_hub_id', $taskHubId)
                ->whereKey($stepId);

            if ($statusId === null) {
                $query->whereNull('task_status_id');
            } else {
                $query->where('task_status_id', $statusId);
            }

            $query->update(['kanban_order' => $index + 1]);
        }
    }
}
