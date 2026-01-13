<?php

namespace App\Services\Task;

use App\Models\Task\TaskType;

class TaskTypeService
{
    public function create(array $data): TaskType
    {
        return TaskType::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
        ]);
    }

    public function update(int $id, array $data): TaskType
    {
        $taskType = TaskType::findOrFail($id);

        $taskType->update([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
        ]);

        return $taskType;
    }

    public function delete(TaskType $taskType): void
    {
        $taskType->delete();
    }
}
