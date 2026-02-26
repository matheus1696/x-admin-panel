<?php

namespace App\Models\Configuration\Establishment\EstablishmentType;

use App\Models\Configuration\Establishment\Establishment\Establishment;
use App\Models\Traits\HasActive;
use App\Models\Traits\HasTitleFilter;
use App\Models\Traits\HasUuid;
use App\Models\Traits\HasUuidRouteKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EstablishmentType extends Model
{
    use HasActive, HasTitleFilter, HasUuid, HasUuidRouteKey;

    protected $fillable = [
        'title',
        'is_active',
    ];

    public function establishments(): HasMany
    {
        return $this->hasMany(Establishment::class, 'type_establishment_id');
    }
}
