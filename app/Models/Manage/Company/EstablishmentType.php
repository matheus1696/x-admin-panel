<?php

namespace App\Models\Manage\Company;

use Illuminate\Database\Eloquent\Model;

class EstablishmentType extends Model
{
    //

    protected $fillable = [
        'title',
        'status',
    ];

    public function Establishment(){
        return $this->belongsTo(Establishment::class,'id','type_establishment_id');
    }
}
