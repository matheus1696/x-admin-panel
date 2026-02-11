<?php

namespace App\Services\Task;

use App\Models\Task\Task;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TaskService
{
    public function find(int $id): Task
    {
        return Task::findOrFail($id);
    }

    public function index(array $filters): LengthAwarePaginator
    {
        $query = Task::query()->with(['user','taskCategory','taskPriority','taskStatus',]);

        // Filtra pelo título
        if ($filters['title']) {
            $query->where('title', 'like', '%' . $filters['title'] . '%');
        }

        return $query->orderBy('code')->paginate($filters['perPage']);
    }

    public function create(array $data): Task
    {
        if ($data['task_status_id'] == 2) {            
            $data['started_at'] = now();
        }

        $task = Task::create($data);
        return $task;
    }

    public function update(int $id, array $data): Task
    {
        $task = Task::findOrFail($id);
        $task->update($data);
        return $task;
    }
}
