<?php

namespace App\Validation\Task;

class TaskStepRules
{
    public static function store(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
        ];
    }

    public static function update(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
        ];
    }
}
