<?php

namespace App\Validation\Organization\Workflow;

class WorkflowStepRules
{
    public static function store(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'deadline_days' => ['required', 'min:1'],
            'organization_id' => ['nullable'],
            'required' => ['nullable'],
            'allow_parallel' => ['nullable'],
        ];
    }

    public static function update(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'deadline_days' => ['required', 'min:1'],
            'organization_id' => ['nullable'],
            'required' => ['nullable'],
            'allow_parallel' => ['nullable'],
        ];
    }
}
