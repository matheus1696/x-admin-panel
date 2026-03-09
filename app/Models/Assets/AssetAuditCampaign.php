<?php

namespace App\Models\Assets;

use App\Models\Administration\User\User;
use App\Models\Configuration\Establishment\Establishment\Department;
use App\Models\Configuration\Establishment\Establishment\Establishment;
use App\Models\Configuration\FinancialBlock\FinancialBlock;
use App\Models\Traits\HasUuid;
use App\Models\Traits\HasUuidRouteKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssetAuditCampaign extends Model
{
    use HasUuid, HasUuidRouteKey;

    protected $fillable = [
        'uuid',
        'title',
        'status',
        'unit_id',
        'sector_id',
        'financial_block_id',
        'start_date',
        'due_date',
        'started_at',
        'finished_at',
        'created_user_id',
        'finished_user_id',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'due_date' => 'date',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(AssetAuditCampaignItem::class, 'asset_audit_campaign_id');
    }

    public function issues(): HasMany
    {
        return $this->hasMany(AssetAuditIssue::class, 'asset_audit_campaign_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Establishment::class, 'unit_id');
    }

    public function sector(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'sector_id');
    }

    public function financialBlock(): BelongsTo
    {
        return $this->belongsTo(FinancialBlock::class, 'financial_block_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_user_id');
    }

    public function finishedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'finished_user_id');
    }
}

