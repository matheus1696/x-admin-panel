<?php

namespace App\Models\Assets;

use App\Enums\Assets\AssetEventType;
use App\Models\Administration\User\User;
use App\Models\Configuration\Establishment\Establishment\Department;
use App\Models\Configuration\Establishment\Establishment\Establishment;
use App\Models\Traits\HasUuid;
use App\Models\Traits\HasUuidRouteKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetEvent extends Model
{
    use HasUuid, HasUuidRouteKey;

    protected $fillable = [
        'uuid',
        'asset_id',
        'type',
        'from_state',
        'to_state',
        'from_unit_id',
        'to_unit_id',
        'from_sector_id',
        'to_sector_id',
        'actor_user_id',
        'notes',
        'payload',
    ];

    protected function casts(): array
    {
        return [
            'type' => AssetEventType::class,
            'payload' => 'array',
        ];
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }

    public function fromUnit(): BelongsTo
    {
        return $this->belongsTo(Establishment::class, 'from_unit_id');
    }

    public function toUnit(): BelongsTo
    {
        return $this->belongsTo(Establishment::class, 'to_unit_id');
    }

    public function fromSector(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'from_sector_id');
    }

    public function toSector(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'to_sector_id');
    }
}
