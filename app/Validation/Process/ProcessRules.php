<?php

namespace App\Validation\Process;

class ProcessRules
{
    public static function store(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'workflow_id' => ['required', 'integer', 'exists:workflows,id'],
        ];
    }
}
