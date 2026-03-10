<?php

namespace App\Models\Process;

use App\Models\Administration\User\User;
use App\Models\Organization\OrganizationChart\OrganizationChart;
use App\Models\Organization\Workflow\Workflow;
use App\Models\Traits\HasUuid;
use App\Models\Traits\HasUuidRouteKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Process extends Model
{
    use HasUuid, HasUuidRouteKey;

    protected $fillable = [
        'uuid',
        'code',
        'title',
        'description',
        'organization_id',
        'workflow_id',
        'opened_by',
        'owner_id',
        'priority',
        'status',
        'started_at',
        'closed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(OrganizationChart::class, 'organization_id');
    }

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class, 'workflow_id');
    }

    public function openedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(ProcessEvent::class)->orderByDesc('created_at');
    }

    protected static function booted(): void
    {
        static::created(function (self $process): void {
            $process->update([
                'code' => 'PRC'.str_pad((string) $process->id, 7, '0', STR_PAD_LEFT),
            ]);
        });
    }
}
