<?php

namespace App\Models\Manage\Company;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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

    //Criação do Filter Title
    public function setNameAttribute($value)
    {
        $this->attributes['title'] = $value;
        $this->attributes['title_filter'] = Str::ascii(strtolower($value));
    }
}
