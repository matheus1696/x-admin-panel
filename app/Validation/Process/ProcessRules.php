<?php

namespace App\Validation\Process;

use App\Enums\Process\ProcessStatus;
use Illuminate\Validation\Rule;

class ProcessRules
{
    public static function store(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'workflow_id' => ['nullable', 'integer', 'exists:workflows,id'],
        ];
    }

    public static function changeStatus(): array
    {
        return [
            'status' => ['required', Rule::in(array_map(fn (ProcessStatus $status) => $status->value, ProcessStatus::cases()))],
            'note' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
