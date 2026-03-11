<?php

namespace App\Models\Process;

use App\Models\Organization\OrganizationChart\OrganizationChart;
use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcessStep extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'process_id',
        'step_order',
        'title',
        'organization_id',
        'deadline_days',
        'required',
        'is_current',
        'status',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'step_order' => 'integer',
        'deadline_days' => 'integer',
        'required' => 'boolean',
        'is_current' => 'boolean',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function process(): BelongsTo
    {
        return $this->belongsTo(Process::class, 'process_id');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(OrganizationChart::class, 'organization_id');
    }
}
