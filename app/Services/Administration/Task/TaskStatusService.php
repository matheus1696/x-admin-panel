<?php

namespace App\Services\Administration\Task;

use App\Models\Administration\Task\TaskStatus;
use Illuminate\Support\Collection;

class TaskStatusService
{
    public function find(int $id): TaskStatus
    {
        return TaskStatus::findOrFail($id);
    }

    public function index(): Collection
    {
        return TaskStatus::where('is_active', true)->orderBy('title')->get();
    }

    public function create(array $data): TaskStatus
    {
        return TaskStatus::create($data);
    }

    public function update(int $id, array $data): TaskStatus
    {
        $taskStatus = TaskStatus::findOrFail($id);
        $taskStatus->update($data);
        return $taskStatus;
    }

    public function delete(TaskStatus $taskStatus): void
    {
        $taskStatus->delete();
    }
}
