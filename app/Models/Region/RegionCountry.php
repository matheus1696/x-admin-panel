<?php

namespace App\Models\Region;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegionCountry extends Model
{
    use HasFactory;

    protected $table = 'region_countries';

    protected $fillable =
    [
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

