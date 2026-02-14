<?php

namespace App\Validation\Task;

use App\Models\Task\TaskStep;
use Carbon\Carbon;

class TaskStepRules
{
    public static function store(): array
    {
        return [
            'title' => 'required|min:4',
            'user_id' => 'nullable|exists:users,id',
            'organization_id' => 'nullable|exists:organization_charts,id',
            'task_priority_id' => 'nullable|exists:task_priorities,id',
            'task_step_status_id' => 'nullable|exists:task_step_statuses,id',
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
        ];
    }

    public static function description(): array
    {
        return [
            'description' => 'nullable|string|max:1000',
        ];
    }

    public static function organizationResponsable(): array
    {
        return [
            'organization_responsable_id' => 'nullable|exists:organization_charts,id',
        ];
    }

    public static function responsable(): array
    {
        return [
            'responsable_id' => 'nullable|exists:users,id',
        ];
    }

    public static function category(): array
    {
        return [
            'list_category_id' => 'nullable|exists:task_categories,id',
        ];
    }

    public static function priority(): array
    {
        return [
            'list_priority_id' => 'nullable|exists:task_priorities,id',
        ];
    }

    public static function status(): array
    {
        return [
            'list_status_id' => 'nullable|exists:task_step_statuses,id',
        ];
    } 

    public static function startedAt(): array
    {
        return [
            'started_at' => [
                'nullable',
                'date',
                'after_or_equal:' . Carbon::now()->subYears(1)->format('Y-m-d'),
                'before_or_equal:' . Carbon::now()->format('Y-m-d'),
            ],
        ];
    }

    public static function deadlineAt(): array
    {
        return [
            'deadline_at' => 'date',
        ];
    }

    public static function storeComment(): array
    {
        return [
            'comment' => 'required|string|max:2000'
        ];
    }
}
