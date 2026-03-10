<?php

namespace App\Models\TimeClock;

use App\Models\Administration\User\User;
use App\Models\Traits\HasUuid;
use App\Models\Traits\HasUuidRouteKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeClockEntry extends Model
{
    use HasUuid, HasUuidRouteKey;

    protected $fillable = [
        'uuid',
        'user_id',
        'occurred_at',
        'photo_path',
        'latitude',
        'longitude',
        'accuracy',
        'device_meta',
        'status',
        'location_id',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
        'accuracy' => 'float',
        'device_meta' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(TimeClockLocation::class, 'location_id');
    }
}
