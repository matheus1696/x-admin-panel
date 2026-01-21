<?php

namespace App\Models\Traits;

trait HasStatus
{
    public function toggleStatus(): self {
        $this->status = ! $this->status;
        $this->save();
        return $this;
    }
}
