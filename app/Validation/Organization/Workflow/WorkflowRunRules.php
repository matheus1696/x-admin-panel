<?php

namespace App\Validation\Organization\Workflow;

class WorkflowRunRules
{
    public static function store(): array
    {
        return [
            'title' => 'required|min:4',
            'description' => 'nullable|min:10',
            'workflow_id' => 'required'
        ];
    }

    public static function update(): array
    {
        return [
            'title' => 'required|min:4',
            'description' => 'nullable|min:10',
        ];
    }
}
