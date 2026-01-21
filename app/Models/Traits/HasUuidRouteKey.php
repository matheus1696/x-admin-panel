<?php

namespace App\Models\Traits;

trait HasUuidRouteKey
{
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
