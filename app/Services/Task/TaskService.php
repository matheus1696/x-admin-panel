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
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaskService
{
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
            ->with(['members.user', 'organizations'])
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

        TaskHubMember::firstOrCreate([
            'task_hub_id' => $taskHub->id,
            'user_id' => $memberUserId,
        ]);

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

        if ($data['task_status_id'] == 2) {
            $data['started_at'] = now();
        }

        $data['kanban_order'] = $this->nextKanbanOrder($taskHub->id, $data['task_status_id']);
        $data['task_hub_id'] = $taskHub->id;
        $data['created_user_id'] = Auth::user()->id;
        $task = Task::create($data);

        TaskActivity::create([
            'task_id' => $task->id,
            'user_id' => Auth::user()->id,
            'type' => 'created',
            'description' => 'Tarefa criada por '.Auth::user()->name,
        ]);
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
            ->whereIn('title', ['Em andamento', 'Concluído', 'Cancelado'])
            ->pluck('id', 'title');

        $inProgressStatusId = $statusIds['Em andamento'] ?? null;
        $completedStatusId = $statusIds['Concluído'] ?? null;
        $cancelledStatusId = $statusIds['Cancelado'] ?? null;

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

        $terminalTaskTitles = ['Concluído', 'Cancelado'];
        $terminalStepTitles = ['Concluída', 'Cancelada'];

        $taskStatuses = TaskStatus::query()
            ->whereNotIn('title', $terminalTaskTitles)
            ->orderBy('id')
            ->get(['id', 'title', 'color']);

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
            ->whereNotIn('title', $terminalStepTitles)
            ->orderBy('id')
            ->get(['id', 'title', 'color']);

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
            ->whereIn('title', $terminalStepTitles)
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
                'users' => [],
                'organizations' => [],
                'overdue_tasks' => [],
            ];
        }

        $baseTaskQuery = Task::query()->whereIn('task_hub_id', $hubIds);
        $terminalStatusIds = $this->terminalStatusIds();
        $total = (clone $baseTaskQuery)->count();

        $taskStatuses = TaskStatus::query()
            ->orderBy('id')
            ->get(['id', 'title', 'color']);

        $taskStatusCounts = (clone $baseTaskQuery)
            ->select('task_status_id', DB::raw('count(*) as total'))
            ->groupBy('task_status_id')
            ->get()
            ->keyBy('task_status_id');

        $statuses = $taskStatuses
            ->map(function (TaskStatus $status) use ($taskStatusCounts) {
                return [
                    'label' => $status->title,
                    'total' => (int) ($taskStatusCounts[$status->id]->total ?? 0),
                    'color' => $this->colorToHex($status->color),
                ];
            })
            ->filter(fn (array $status) => $status['total'] > 0)
            ->values();

        $tasksWithoutStatus = (int) data_get($taskStatusCounts->get(null), 'total', 0);

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

        if ($terminalStatusIds !== []) {
            $overdueQuery->whereNotIn('task_status_id', $terminalStatusIds);
        }

        $overdue = $overdueQuery->count();

        $responsibleCounts = (clone $baseTaskQuery)
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

        $tasksByUser = $responsibleCounts
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

        $stepOrganizationCounts = TaskStep::query()
            ->whereIn('task_hub_id', $hubIds)
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

        $hubLabels = TaskHub::query()
            ->whereIn('id', $hubIds)
            ->get(['id', 'acronym', 'title'])
            ->mapWithKeys(fn (TaskHub $hub) => [$hub->id => $hub->acronym ?: $hub->title]);

        $overdueTasks = Task::query()
            ->whereIn('task_hub_id', $hubIds)
            ->whereNotNull('deadline_at')
            ->where('deadline_at', '<', now())
            ->when($terminalStatusIds !== [], fn ($query) => $query->whereNotIn('task_status_id', $terminalStatusIds))
            ->with('user')
            ->orderBy('deadline_at')
            ->limit(6)
            ->get()
            ->map(function (Task $task) use ($hubLabels) {
                return [
                    'id' => $task->id,
                    'code' => $task->code,
                    'title' => $task->title,
                    'deadline_at' => $task->deadline_at?->format('Y-m-d'),
                    'responsible' => $task->user?->name ?? 'Sem responsável',
                    'hub' => $hubLabels[$task->task_hub_id] ?? 'Hub',
                ];
            })
            ->all();

        return [
            'hubs_total' => count($hubIds),
            'total' => $total,
            'overdue' => $overdue,
            'statuses' => $statuses->all(),
            'users' => $tasksByUser,
            'organizations' => $stepsByOrganization,
            'overdue_tasks' => $overdueTasks,
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
            ->orderBy('id')
            ->get();

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
    ): void {
        $taskHub = TaskHub::where('uuid', $hubUuid)->firstOrFail();

        DB::transaction(function () use ($taskHub, $taskId, $fromStatusId, $toStatusId, $sourceOrder, $targetOrder, $reason, $reasonType) {
            $task = Task::query()
                ->where('task_hub_id', $taskHub->id)
                ->lockForUpdate()
                ->findOrFail($taskId);

            $fromStatusId = $fromStatusId === 0 ? null : $fromStatusId;
            $toStatusId = $toStatusId === 0 ? null : $toStatusId;

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
    public function stepKanban(string $id): array
    {
        $taskHub = TaskHub::where('uuid', $id)->firstOrFail();

        $statuses = TaskStepStatus::query()
            ->orderBy('id')
            ->get();

        $steps = TaskStep::query()
            ->where('task_hub_id', $taskHub->id)
            ->where(function ($query): void {
                $query->whereNull('finished_at')
                    ->orWhere('finished_at', '>=', now()->subDays(3));
            })
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

            $previousStatusId = $step->task_status_id;
            $terminalStatusIds = $this->stepTerminalStatusIds();

            if (
                $fromStatusId !== $toStatusId
                && $fromStatusId !== null
                && $toStatusId !== null
                && in_array($fromStatusId, $terminalStatusIds, true)
                && in_array($toStatusId, $terminalStatusIds, true)
            ) {
                return false;
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
                ? (TaskStepStatus::query()->find($toStatusId)?->title ?? 'Status')
                : 'Sem status';

            $description = ($this->actorName()).' moveu a etapa no kanban para '.$toStatusLabel;
            if ($reason && $reasonType === 'completion') {
                $description = ($this->actorName()).' concluiu a etapa: '.$reason;
                $this->recordTaskCommentForCompletedStep($step, $reason);
                $this->recordTaskStepCompletionActivity($step);
            }
            if ($reason && $reasonType === 'cancellation') {
                $description = ($this->actorName()).' cancelou a etapa: '.$reason;
                $this->recordTaskCommentForCancelledStep($step, $reason);
                $this->recordTaskStepCancellationActivity($step);
            }
            if ($reason && $reasonType === 'reopen') {
                $description = ($this->actorName()).' reabriu a etapa: '.$reason;
                $this->recordTaskCommentForReopenedStep($step, $reason);
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
            ->where('title', 'Concluída')
            ->value('id');

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
        TaskStepActivity::create([
            'task_step_id' => $stepId,
            'user_id' => Auth::user()?->id,
            'type' => 'comment',
            'description' => $comment,
        ]);
    }

    public function changeStatus(int $taskId, ?int $statusId, ?string $description = null, string $type = 'status_change'): Task
    {
        $task = Task::findOrFail($taskId);
        $previousStatusId = $task->task_status_id;

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

        if ($statusId !== null && in_array($statusId, $this->stepInProgressStatusIds(), true) && $step->started_at === null) {
            $updates['started_at'] = now();
        }

        $terminalStatusIds = $this->stepTerminalStatusIds();

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
        if ($statusId === null || ! in_array($statusId, $this->stepInProgressStatusIds(), true)) {
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

        return $previousStep->finished_at === null;
    }

    private function applyStatusUpdate(Task $task, ?int $statusId): void
    {
        $updates = [
            'task_status_id' => $statusId,
        ];

        if ($statusId === 2 && $task->started_at === null) {
            $updates['started_at'] = now();
        }

        $terminalStatusIds = $this->terminalStatusIds();

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
    private function terminalStatusIds(): array
    {
        return TaskStatus::query()
            ->whereIn('title', ['Concluído', 'Cancelado'])
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    /**
     * @return array<int>
     */
    private function stepTerminalStatusIds(): array
    {
        return TaskStepStatus::query()
            ->whereIn('title', ['Concluída', 'Cancelada'])
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    /**
     * @return array<int>
     */
    private function stepInProgressStatusIds(): array
    {
        return TaskStepStatus::query()
            ->whereIn('title', ['Em andamento', 'Em execucao', 'Em execução'])
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
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
