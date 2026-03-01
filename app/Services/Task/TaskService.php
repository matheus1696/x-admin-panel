<?php

namespace App\Services\Task;

use App\Models\Administration\Task\TaskStatus;
use App\Models\Administration\Task\TaskStepStatus;
use App\Models\Administration\User\User;
use App\Models\Organization\OrganizationChart\OrganizationChart;
use App\Models\Task\Task;
use App\Models\Task\TaskActivity;
use App\Models\Task\TaskHub;
use App\Models\Task\TaskStep;
use App\Models\Task\TaskStepActivity;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaskService
{
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

    public function index(string $id, array $filters): LengthAwarePaginator
    {
        $taskHub = TaskHub::where('uuid', $id)->firstOrFail();

        $query = Task::query();

        $query->where('task_hub_id', $taskHub->id)
            ->whereNull('finished_at');

        // Filtra pelo título
        if ($filters['title']) {
            $query->where('title', 'like', '%'.$filters['title'].'%');
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
            ->values()
            ->all();

        $stepOrganizationCounts = TaskStep::query()
            ->where('task_hub_id', $taskHub->id)
            ->select('organization_id', DB::raw('count(*) as total'))
            ->groupBy('organization_id')
            ->orderByDesc('total')
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
    ): void {
        $taskHub = TaskHub::where('uuid', $hubUuid)->firstOrFail();

        DB::transaction(function () use ($taskHub, $stepId, $fromStatusId, $toStatusId, $sourceOrder, $targetOrder, $reason, $reasonType) {
            $step = TaskStep::query()
                ->where('task_hub_id', $taskHub->id)
                ->lockForUpdate()
                ->findOrFail($stepId);

            $fromStatusId = $fromStatusId === 0 ? null : $fromStatusId;
            $toStatusId = $toStatusId === 0 ? null : $toStatusId;

            $previousStatusId = $step->task_status_id;

            if ($fromStatusId !== $toStatusId) {
                $updates = ['task_status_id' => $toStatusId];
                $terminalStatusIds = $this->stepTerminalStatusIds();

                if ($toStatusId !== null && in_array($toStatusId, $terminalStatusIds, true) && $step->finished_at === null) {
                    $updates['finished_at'] = now();
                }

                if (($toStatusId === null || ! in_array($toStatusId, $terminalStatusIds, true)) && $step->finished_at !== null) {
                    $updates['finished_at'] = null;
                }

                $step->update($updates);
            }

            if ($fromStatusId === $toStatusId) {
                $this->applyKanbanStepOrder($taskHub->id, $toStatusId, $targetOrder);
            } else {
                $this->applyKanbanStepOrder($taskHub->id, $fromStatusId, $sourceOrder);
                $this->applyKanbanStepOrder($taskHub->id, $toStatusId, $targetOrder);
            }

            $description = ($this->actorName()).' moveu a etapa no kanban';
            if ($reason && $reasonType === 'completion') {
                $description = ($this->actorName()).' concluiu a etapa: '.$reason;
            }
            if ($reason && $reasonType === 'cancellation') {
                $description = ($this->actorName()).' cancelou a etapa: '.$reason;
            }
            if ($reason && $reasonType === 'reopen') {
                $description = ($this->actorName()).' reabriu a etapa: '.$reason;
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
        });
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
