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
        $query = Task::query();

        // Filtra pelo título
        if ($filters['title']) {
            $query->where('title', 'like', '%' . $filters['title'] . '%');
        }

        // Filtra pelo status
        if ($filters['workflow_run_status_id'] != 'all') {
            $query->where('workflow_run_status_id', $filters['workflow_run_status_id']);
        }

        return $query->orderBy('code')->paginate($filters['perPage']);
    }

    public function create(array $data): Task
    {
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
