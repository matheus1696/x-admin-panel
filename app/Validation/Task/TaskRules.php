<?php

namespace App\Validation\Task;

class TaskRules
{
    public static function store(): array
    {
        return [
            'title' => 'required|min:4',
            'description' => 'nullable|min:10',
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
