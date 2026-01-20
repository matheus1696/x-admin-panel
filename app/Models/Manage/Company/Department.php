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

    public function establishment(){
        return $this->belongsTo(Establishment::class,'establishment_id');
    }

    //Criação do Filter Title
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;
        $this->attributes['filter'] = Str::ascii(strtolower($value));
    }
}
