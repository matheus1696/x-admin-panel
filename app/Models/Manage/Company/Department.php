<?php

namespace App\Models\Manage\Company;

use Illuminate\Database\Eloquent\Model;

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
}
