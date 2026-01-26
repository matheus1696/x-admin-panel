<?php

namespace App\Validation\Task;

use Illuminate\Validation\Rule;

class TaskStepRules
{
    public static function store(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'deadline_days' => ['required', 'integer', 'min:0'],
            'required' => ['boolean'],
            'allow_parallel' => ['boolean'],
        ];
    }

    public static function update(int $workflowStepId): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'deadline_days' => ['required', 'integer', 'min:0'],
            'required' => ['boolean'],
            'allow_parallel' => ['boolean'],
        ];
    }
}
