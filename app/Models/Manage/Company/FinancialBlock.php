<?php

namespace App\Models\Manage\Company;

use Illuminate\Database\Eloquent\Model;

class FinancialBlock extends Model
{
    //
    protected $fillable = [
        'title',
        'acronym',
        'color',
        'status',
    ];
}
