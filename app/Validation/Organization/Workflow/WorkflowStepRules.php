<?php

namespace App\Validation\Organization\Workflow;

class WorkflowStepRules
{
    public static function store(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'deadline_days' => ['required', 'integer', 'min:1'],
            'organization_id' => ['required', 'integer', 'exists:organization_charts,id'],
            'required' => ['nullable', 'boolean'],
            'allow_parallel' => ['nullable', 'boolean'],
        ];
    }

    public static function update(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'deadline_days' => ['required', 'integer', 'min:1'],
            'organization_id' => ['required', 'integer', 'exists:organization_charts,id'],
            'required' => ['nullable', 'boolean'],
            'allow_parallel' => ['nullable', 'boolean'],
        ];
    }
}
