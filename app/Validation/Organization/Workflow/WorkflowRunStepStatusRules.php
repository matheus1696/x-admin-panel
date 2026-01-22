<?php

namespace App\Validation\Organization\Workflow;

class WorkflowRunStepStatusRules
{
    public static function store(): array
    {
        return [
            'title' => 'required|min:4',
            'color' => 'required',
        ];
    }

    public static function update(): array
    {
        return [
            'title' => 'required|min:4',
            'color' => 'required',
        ];
    }
}
