<?php

namespace App\Services\Administration\Task;

use App\Models\Administration\Task\TaskStepStatus;
use Illuminate\Support\Collection;

class TaskStepStatusService
{
    public function find(int $id): TaskStepStatus
    {
        return TaskStepStatus::findOrFail($id);
    }

    public function index(): Collection
    {
        return TaskStepStatus::orderBy('title')->get();
    }

    public function create(array $data): TaskStepStatus
    {
        return TaskStepStatus::create($data);
    }

    public function update(int $id, array $data): TaskStepStatus
    {
        $taskStepStatus = TaskStepStatus::findOrFail($id);
        $taskStepStatus->update($data);
        return $taskStepStatus;
    }

    public function delete(TaskStepStatus $taskStepStatus): void
    {
        $taskStepStatus->delete();
    }
}
