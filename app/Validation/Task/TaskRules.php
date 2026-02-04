<?php

namespace App\Validation\Task;

class TaskRules
{
    public static function store(): array
    {
        return [
            'title' => 'required|min:4',
            'user_id' => 'nullable|exists:users,id',
            'task_category_id' => 'nullable|exists:task_categories,id',
            'task_priority_id' => 'nullable|exists:task_priorities,id',
            'task_status_id' => 'nullable|exists:task_statuses,id',
            'deadline_at' => 'nullable|date',
        ];
    }

    public static function update(): array
    {
        return [
            'title' => 'required|min:4',
            'user_id' => 'nullable|exists:users,id',
            'task_category_id' => 'nullable|exists:task_categories,id',
            'task_priority_id' => 'nullable|exists:task_priorities,id',
            'task_status_id' => 'nullable|exists:task_statuses,id',
            'deadline_at' => 'nullable|date',
        ];
    }

    public static function responsable(): array
    {
        return [
            'user_id' => 'nullable|exists:users,id',
        ];
    }
}
