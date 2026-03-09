<?php

namespace App\Models\Assets;

use App\Models\Administration\User\User;
use App\Models\Traits\HasUuid;
use App\Models\Traits\HasUuidRouteKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AssetAuditCampaignItem extends Model
{
    use HasUuid, HasUuidRouteKey;

    protected $fillable = [
        'uuid',
        'asset_audit_campaign_id',
        'asset_id',
        'status',
        'audited_at',
        'audited_user_id',
        'photo_path',
        'notes',
        'expected_unit_id',
        'expected_sector_id',
        'observed_unit',
        'observed_sector',
    ];

    protected function casts(): array
    {
        return [
            'audited_at' => 'datetime',
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(AssetAuditCampaign::class, 'asset_audit_campaign_id');
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function auditedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'audited_user_id');
    }

    public function issue(): HasOne
    {
        return $this->hasOne(AssetAuditIssue::class, 'asset_audit_campaign_item_id');
    }
}

