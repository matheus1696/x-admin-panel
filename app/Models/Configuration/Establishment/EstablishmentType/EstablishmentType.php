<?php

namespace App\Models\Configuration\Establishment\EstablishmentType;

use App\Models\Configuration\Establishment\Establishment\Establishment;
use App\Models\Traits\HasStatus;
use App\Models\Traits\HasTitleFilter;
use App\Models\Traits\HasUuid;
use App\Models\Traits\HasUuidRouteKey;
use Illuminate\Database\Eloquent\Model;

class EstablishmentType extends Model
{
    use HasStatus, HasTitleFilter, HasUuid, HasUuidRouteKey;

    protected $fillable = [
        'title',
        'status',
    ];

    public function establishment(){
        return $this->belongsTo(Establishment::class,'id','type_establishment_id');
    }
}
