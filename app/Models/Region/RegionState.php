<?php

namespace App\Models\Region;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegionState extends Model
{
    use HasFactory;

    protected $table = 'region_states';

    protected $fillable =
    [
        'acronym',
        'code_uf',
        'code_ddd',
        'state',
        'filter',
        'status',
        'code_ddd',
        'country_id',
    ];

    public function RegionCountry(){
        return $this->belongsTo(RegionCountry::class,'country_id','id');
    }
}

