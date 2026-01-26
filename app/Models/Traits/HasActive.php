<?php

namespace App\Models\Traits;

trait HasActive
{
    public function toggleStatus(): self {
        $this->is_active = ! $this->is_active;
        $this->save();
        return $this;
    }
}
