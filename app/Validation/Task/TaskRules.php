<?php

namespace App\Validation\Task;

class TaskRules
{
    public static function store(): array
    {
        return [
            'title' => 'required|min:4',
        ];
    }

    public static function update(): array
    {
        return [
            'title' => 'required|min:4',
        ];
    }
}
