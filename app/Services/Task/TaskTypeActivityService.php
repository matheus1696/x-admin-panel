<?php

namespace App\Services\Task;

use App\Models\Task\TaskTypeActivity;

class TaskTypeActivityService
{
    public function create(array $data): TaskTypeActivity
    {
        return TaskTypeActivity::create([
            'task_type_id' => $data['task_type_id'],
            'title' => $data['title'],
            'deadline_days' => $data['deadline_days'] ?? null,
            'order' => $data['order'],
        ]);
    }

    public function update(int $id, array $data): TaskTypeActivity
    {
        $taskType = TaskTypeActivity::findOrFail($id);

        $taskType->update([
            'title' => $data['title'],
            'deadline_days' => $data['deadline_days'] ?? null,
        ]);

        return $taskType;
    }

    public function status(int $id): TaskTypeActivity
    {
        $taskType = TaskTypeActivity::findOrFail($id);
        return $taskType->toggleStatus();
    }

    public function delete(TaskTypeActivity $taskTypeActivity): void
    {
        $taskTypeActivity->delete();
    }
}
