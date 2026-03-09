<?php

namespace App\Models\Assets;

use App\Models\Administration\User\User;
use App\Models\Configuration\Establishment\Establishment\Department;
use App\Models\Configuration\Establishment\Establishment\Establishment;
use App\Models\Traits\HasUuid;
use App\Models\Traits\HasUuidRouteKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssetReleaseOrder extends Model
{
    use HasUuid, HasUuidRouteKey;

    protected $fillable = [
        'uuid',
        'code',
        'status',
        'to_unit_id',
        'to_sector_id',
        'requester_name',
        'receiver_name',
        'notes',
        'total_assets',
        'released_at',
        'released_user_id',
    ];

    protected function casts(): array
    {
        return [
            'total_assets' => 'integer',
            'released_at' => 'datetime',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(AssetReleaseOrderItem::class, 'asset_release_order_id');
    }

    public function toUnit(): BelongsTo
    {
        return $this->belongsTo(Establishment::class, 'to_unit_id');
    }

    public function toSector(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'to_sector_id');
    }

    public function releasedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'released_user_id');
    }
}

