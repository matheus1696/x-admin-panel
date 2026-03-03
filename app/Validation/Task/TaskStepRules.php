<?php

namespace App\Validation\Task;

use Carbon\Carbon;
use Illuminate\Validation\Rule;

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

    /**
     * @param array<int>|null $allowedOrganizationIds
     */
    public static function organizationResponsable(?array $allowedOrganizationIds = null): array
    {
        if ($allowedOrganizationIds !== null) {
            return [
                'organization_responsable_id' => ['nullable', Rule::in($allowedOrganizationIds)],
            ];
        }

        return [
            'organization_responsable_id' => 'nullable|exists:organization_charts,id',
        ];
    }

    /**
     * @param array<int>|null $allowedUserIds
     */
    public static function responsable(?array $allowedUserIds = null): array
    {
        if ($allowedUserIds !== null) {
            return [
                'responsable_id' => ['nullable', Rule::in($allowedUserIds)],
            ];
        }

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
