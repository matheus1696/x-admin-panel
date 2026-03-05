<?php

namespace App\Models\Administration\Supplier;

use App\Models\Configuration\Region\RegionCity;
use App\Models\Configuration\Region\RegionState;
use App\Models\Traits\HasActive;
use App\Models\Traits\HasTitleFilter;
use App\Models\Traits\HasUuid;
use App\Models\Traits\HasUuidRouteKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Supplier extends Model
{
    use HasActive, HasTitleFilter, HasUuid, HasUuidRouteKey;

    protected $fillable = [
        'uuid',
        'title',
        'trade_name',
        'filter',
        'document',
        'email',
        'phone',
        'phone_secondary',
        'address_street',
        'address_number',
        'address_district',
        'state_id',
        'city_id',
        'address_zipcode',
        'is_active',
    ];

    public function state(): BelongsTo
    {
        return $this->belongsTo(RegionState::class, 'state_id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(RegionCity::class, 'city_id');
    }
}
