<?php

namespace App\Models\Configuration\FinancialBlock;

use App\Models\Traits\HasActive;
use App\Models\Traits\HasTitleFilter;
use App\Models\Traits\HasUuid;
use App\Models\Traits\HasUuidRouteKey;
use Illuminate\Database\Eloquent\Model;

class FinancialBlock extends Model
{
    use HasActive, HasTitleFilter, HasUuid, HasUuidRouteKey;

    protected $fillable = [
        'title',
        'acronym',
        'color',
        'is_active',
    ];
}
