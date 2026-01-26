<?php

namespace App\Validation\Administration\Task;

class TaskStatusRules
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
