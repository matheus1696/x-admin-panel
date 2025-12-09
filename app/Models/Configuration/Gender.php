<?php

namespace App\Models\Configuration;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Gender extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'title_filter',
        'status',
    ];

    //Criação do Filter Title
    public function setNameAttribute($value)
    {
        $this->attributes['title'] = $value;
        $this->attributes['title_filter'] = Str::ascii(strtolower($value));
    }
}
