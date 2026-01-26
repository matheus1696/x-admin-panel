<?php

namespace App\Validation\Administration\Task;

class TaskStepStatusRules
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
