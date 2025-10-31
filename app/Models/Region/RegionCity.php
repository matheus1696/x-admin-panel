<?php

namespace App\Models\Region;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegionCity extends Model
{
    use HasFactory;

    protected $table = 'region_cities';

    protected $fillable =
    [
        'code',
        'city',
        'filter',
        'code_cep',
        'status',
        'state_id',
    ];

    public function RegionState(){
        return $this->belongsTo(RegionState::class,'state_id','id');
    }
}

