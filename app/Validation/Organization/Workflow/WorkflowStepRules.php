<?php

namespace App\Validation\Organization\Workflow;

use Illuminate\Validation\Rule;

class WorkflowStepRules
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
