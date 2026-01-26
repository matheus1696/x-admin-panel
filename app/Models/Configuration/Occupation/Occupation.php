<?php

namespace App\Models\Configuration\Occupation;

use App\Models\Traits\HasActive;
use App\Models\Traits\HasTitleFilter;
use App\Models\Traits\HasUuid;
use App\Models\Traits\HasUuidRouteKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Occupation extends Model
{
    use HasFactory, HasActive, HasTitleFilter, HasUuid, HasUuidRouteKey;

    protected $fillable = [
        'code',
        'title',
        'filter',
        'is_active',
    ];
}

