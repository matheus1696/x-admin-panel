<?php

namespace App\Models\Organization\Workflow;

use App\Models\Traits\HasStatus;
use App\Models\Traits\HasTitleFilter;
use App\Models\Traits\HasUuid;
use App\Models\Traits\HasUuidRouteKey;
use Illuminate\Database\Eloquent\Model;

class WorkflowStep extends Model
{
    use HasStatus, HasTitleFilter, HasUuid, HasUuidRouteKey;
    
    protected $fillable = [
        'workflow_id',
        'title',
        'filter',
        'step_order',
        'deadline_days',
        'required',
        'allow_parallel',
        'step_type',
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

        public function workflowRunSteps()
    {
        return $this->hasMany(WorkflowRunStep::class);
    }
}
