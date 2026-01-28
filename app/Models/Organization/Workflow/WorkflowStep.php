<?php

namespace App\Models\Organization\Workflow;

use App\Models\Traits\HasActive;
use App\Models\Traits\HasTitleFilter;
use App\Models\Traits\HasUuid;
use App\Models\Traits\HasUuidRouteKey;
use Illuminate\Database\Eloquent\Model;

class WorkflowStep extends Model
{
    use HasActive, HasTitleFilter, HasUuid, HasUuidRouteKey;
    
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

    /**
     * Process workflow
     */
    public function workflow()
    {
        return $this->belongsTo(Workflow::class);
    }
}
