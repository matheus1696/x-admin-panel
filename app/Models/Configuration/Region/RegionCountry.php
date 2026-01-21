<?php

namespace App\Models\Configuration\Region;

use App\Models\Traits\HasStatus;
use App\Models\Traits\HasTitleFilter;
use App\Models\Traits\HasUuid;
use App\Models\Traits\HasUuidRouteKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegionCountry extends Model
{
    use HasFactory, HasStatus, HasTitleFilter, HasUuid, HasUuidRouteKey;

    protected $table = 'region_countries';

    protected $fillable = [
        'code',
        'acronym_2',
        'acronym_3',        
        'country',
        'filter',
        'country_ing',
        'filter_country_ing',
        'code_iso',
        'code_ddi',
        'status',
    ];
}

