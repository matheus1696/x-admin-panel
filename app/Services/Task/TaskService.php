<?php

namespace App\Services\Task;

use App\Models\Administration\Task\TaskStepStatus;
use App\Models\Administration\User\User;
use App\Models\Task\Task;
use App\Models\Task\TaskActivity;
use App\Models\Task\TaskHub;
use App\Models\Task\TaskStep;
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
            'taskSteps',
            'taskStepsFinished',
        ])->findOrFail($id);
    }

    public function index(string $id, array $filters): LengthAwarePaginator
    {
        $taskHub = TaskHub::where('uuid', $id)->firstOrFail();

        $query = Task::query();

        $query->where('task_hub_id', $taskHub->id);

        // Filtra pelo título
        if ($filters['title']) {
            $query->where('title', 'like', '%' . $filters['title'] . '%');
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

        $data['task_hub_id'] = $taskHub->id;
        $data['created_user_id'] = Auth::user()->id;
        $task = Task::create($data);

        TaskActivity::create([
            'task_id'     => $task->id,
            'user_id'     => Auth::user()->id,
            'type'        => 'created',
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
            'updated_at' => now()
        ]);
    }

    /**
     * @return array{
     *     total:int,
     *     in_progress:int,
     *     completed:int,
     *     overdue:int,
     *     tasks_by_responsible:array<int, array{label:string, total:int}>,
     *     tasks_by_step_status:array<int, array{label:string, total:int}>
     * }
     */
    public function dashboard(string $id): array
    {
        $taskHub = TaskHub::where('uuid', $id)->firstOrFail();

        $baseQuery = Task::query()->where('task_hub_id', $taskHub->id);

        $total = (clone $baseQuery)->count();
        $inProgress = (clone $baseQuery)
            ->whereNotNull('started_at')
            ->whereNull('finished_at')
            ->count();
        $completed = (clone $baseQuery)
            ->whereNotNull('finished_at')
            ->count();
        $overdue = (clone $baseQuery)
            ->whereNotNull('deadline_at')
            ->whereNull('finished_at')
            ->where('deadline_at', '<', now())
            ->count();

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

        return [
            'total' => $total,
            'in_progress' => $inProgress,
            'completed' => $completed,
            'overdue' => $overdue,
            'tasks_by_responsible' => $tasksByResponsible,
            'tasks_by_step_status' => $tasksByStepStatus,
        ];
    }
}
