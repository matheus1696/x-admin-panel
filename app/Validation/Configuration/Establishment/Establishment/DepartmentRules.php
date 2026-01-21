<?php

namespace App\Validation\Configuration\Establishment\Establishment;

use Illuminate\Validation\Rule;

class DepartmentRules
{
    public static function store(): array
    {
        return [
            'title' => [ 'required', 'string', 'max:255', ],
            'contact' => [ 'required', 'string', 'max:15', Rule::unique('departments', 'contact'), ],
            'extension' => [ 'nullable', 'string', 'max:4', Rule::unique('departments', 'extension'),],
            'type_contact' => [ 'required', 'string', Rule::in(['Without', 'Internal', 'Main']), ],
        ];
    }

    public static function update(?int $departmentId): array
    {
        return [
            'title' => [ 'required', 'string', 'max:255', ],
            'contact' => [ 'required', 'string', 'max:100', Rule::unique('departments', 'contact')->ignore($departmentId), ],
            'extension' => [ 'nullable', 'string', 'max:20', Rule::unique('departments', 'extension')->ignore($departmentId), ],
            'type_contact' => [ 'required', 'string', Rule::in(['Without', 'Internal', 'Main']), ],
        ];
    }
}