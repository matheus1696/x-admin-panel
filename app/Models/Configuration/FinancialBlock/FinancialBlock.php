<?php

namespace App\Models\Configuration\FinancialBlock;

use App\Models\Traits\HasStatus;
use App\Models\Traits\HasTitleFilter;
use App\Models\Traits\HasUuid;
use App\Models\Traits\HasUuidRouteKey;
use Illuminate\Database\Eloquent\Model;

class FinancialBlock extends Model
{
    use HasStatus, HasTitleFilter, HasUuid, HasUuidRouteKey;

    protected $fillable = [
        'title',
        'acronym',
        'color',
        'status',
    ];
}
