<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserGender extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'name_filter',
        'status',
    ];

    //Criação do Filter Name
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['name_filter'] = Str::ascii(strtolower($value));
    }
}
