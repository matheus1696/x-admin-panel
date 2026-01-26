<?php

namespace App\Models\Configuration\Region;

use App\Models\Traits\HasActive;
use App\Models\Traits\HasTitleFilter;
use App\Models\Traits\HasUuid;
use App\Models\Traits\HasUuidRouteKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegionState extends Model
{
    use HasFactory, HasActive, HasTitleFilter, HasUuid, HasUuidRouteKey;

    protected $table = 'region_states';

    protected $fillable = [
        'acronym',
        'code_uf',
        'code_ddd',
        'state',
        'filter',
        'is_active',
        'code_ddd',
        'country_id',
    ];

    public function RegionCountry(){
        return $this->belongsTo(RegionCountry::class,'country_id','id');
    }
}

