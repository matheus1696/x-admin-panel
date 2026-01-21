<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

trait HasUuid
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if (empty($this->uuid)) {
            $this->uuid = (string) Str::uuid();
        }
    }
}
