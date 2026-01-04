<?php

namespace App\Models\Manage\Company;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FinancialBlock extends Model
{
    //
    protected $fillable = [
        'title',
        'acronym',
        'color',
        'status',
    ];   

    //Criação do Filter Title
    public function setNameAttribute($value)
    {
        $this->attributes['title'] = $value;
        $this->attributes['title_filter'] = Str::ascii(strtolower($value));
    }
}
