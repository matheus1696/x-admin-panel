<?php

namespace App\Models\TimeClock;

use App\Models\Configuration\Establishment\Establishment\Establishment;
use App\Models\Traits\HasUuid;
use App\Models\Traits\HasUuidRouteKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TimeClockLocation extends Model
{
    use HasUuid, HasUuidRouteKey;

    protected $fillable = [
        'uuid',
        'name',
        'establishment_id',
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

    public function establishment(): BelongsTo
    {
        return $this->belongsTo(Establishment::class, 'establishment_id');
    }

    public function entries(): HasMany
    {
        return $this->hasMany(TimeClockEntry::class, 'location_id');
    }
}
