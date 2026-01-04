<?php

namespace App\Models\Manage\Company;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Department extends Model
{
    //
    protected $fillable = [
        'title',
        'filter',
        'contact',
        'extension',
        'type_contact',
        'establishment_id'
    ];

    public function CompanyEstablishment(){
        return $this->belongsTo(Establishment::class,'establishment_id','id');
    }    

    //Criação do Filter Title
    public function setNameAttribute($value)
    {
        $this->attributes['title'] = $value;
        $this->attributes['title_filter'] = Str::ascii(strtolower($value));
    }
}
