<?php

namespace App\Models\Organization\Workflow;

use App\Models\Organization\OrganizationChart\OrganizationChart;
use App\Models\Traits\HasActive;
use App\Models\Traits\HasTitleFilter;
use App\Models\Traits\HasUuid;
use App\Models\Traits\HasUuidRouteKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkflowStep extends Model
{
    use HasTitleFilter, HasUuid, HasUuidRouteKey;
    
    protected $fillable = [
        'workflow_id',
        'title',
        'filter',
        'step_order',
        'deadline_days',
        'required',
        'allow_parallel',
        'step_type',
        'organization_id',
    ];

    protected $casts = [
        'required' => 'boolean',
        'allow_parallel' => 'boolean',
        'deadline_days' => 'integer',
        'step_order' => 'integer',
    ];

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(OrganizationChart::class, 'organization_id');
    }
}
