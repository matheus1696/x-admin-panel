<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

trait HasTitleFilter
{
    public function setTitleAttribute(string $value): void {
        $this->attributes['title'] = $value;
        $this->attributes['filter'] = Str::ascii(mb_strtolower($value));
    }
}
