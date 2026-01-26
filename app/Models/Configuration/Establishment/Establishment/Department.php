<?php

namespace App\Models\Configuration\Establishment\Establishment;

use App\Models\Traits\HasTitleFilter;
use App\Models\Traits\HasUuid;
use App\Models\Traits\HasUuidRouteKey;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{    
    use HasTitleFilter, HasUuid, HasUuidRouteKey;
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
}
