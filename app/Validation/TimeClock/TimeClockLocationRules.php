<?php

namespace App\Validation\TimeClock;

class TimeClockLocationRules
{
    public static function store(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'establishment_id' => ['nullable', 'integer', 'exists:establishments,id'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'radius_meters' => ['required', 'integer', 'min:1', 'max:5000'],
            'active' => ['required', 'boolean'],
        ];
    }
}
