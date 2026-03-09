<?php

namespace App\Models\Assets;

use App\Models\Administration\User\User;
use App\Models\Traits\HasUuid;
use App\Models\Traits\HasUuidRouteKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetAuditIssue extends Model
{
    use HasUuid, HasUuidRouteKey;

    protected $fillable = [
        'uuid',
        'asset_audit_campaign_id',
        'asset_audit_campaign_item_id',
        'asset_id',
        'issue_type',
        'status',
        'notes',
        'opened_at',
        'resolved_at',
        'resolved_user_id',
    ];

    protected function casts(): array
    {
        return [
            'opened_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(AssetAuditCampaign::class, 'asset_audit_campaign_id');
    }

    public function campaignItem(): BelongsTo
    {
        return $this->belongsTo(AssetAuditCampaignItem::class, 'asset_audit_campaign_item_id');
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_user_id');
    }
}

