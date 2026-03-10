<?php

namespace App\Models\TimeClock;

use App\Models\Traits\HasUuid;
use App\Models\Traits\HasUuidRouteKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TimeClockLocation extends Model
{
    use HasUuid, HasUuidRouteKey;

    protected $fillable = [
        'uuid',
        'name',
        'latitude',
        'longitude',
        'radius_meters',
        'active',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'radius_meters' => 'integer',
        'active' => 'boolean',
    ];

    public function entries(): HasMany
    {
        return $this->hasMany(TimeClockEntry::class, 'location_id');
    }
}
