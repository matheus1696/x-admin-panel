<?php

namespace App\Services\Task;

use App\Models\Task\Task;
use App\Models\Task\TaskActivity;
use App\Models\Task\TaskHub;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

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
        $taskHub = TaskHub::where('uuid', $id)->first();

        $query = Task::query();

        $query->where('task_hub_id', $taskHub->id);

        // Filtra pelo título
        if ($filters['title']) {
            $query->where('title', 'like', '%' . $filters['title'] . '%');
        }

        $query->with(['user','taskCategory','taskPriority','taskStatus',]);

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
            'update_at' => now()
        ]);
    }
}
