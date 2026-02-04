<?php

namespace App\Validation\Task;

class TaskStepRules
{
    public static function store(): array
    {
        return [
            'title' => 'required|min:4',
            'user_id' => 'nullable|exists:users,id',
            'task_category_id' => 'nullable|exists:task_categories,id',
            'task_priority_id' => 'nullable|exists:task_priorities,id',
            'task_step_status_id' => 'nullable|exists:task_step_statuses,id',
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
            'task_step_status_id' => 'nullable|exists:task_step_statuses,id',
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
