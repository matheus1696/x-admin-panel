<?php

namespace App\Models\Organization\Workflow;

use App\Models\Traits\HasStatus;
use App\Models\Traits\HasTitleFilter;
use App\Models\Traits\HasUuid;
use App\Models\Traits\HasUuidRouteKey;
use Illuminate\Database\Eloquent\Model;

class Workflow extends Model
{
    use HasStatus, HasTitleFilter, HasUuid, HasUuidRouteKey;

    protected $fillable = [
        'title',
        'filter',
        'description',
        'total_estimated_days',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'total_estimated_days' => 'integer',
    ];

    public function workflowSteps(){
        return $this->hasMany(WorkflowStep::class)->orderBy('step_order');
    }

    public function workflowRuns()
    {
        return $this->hasMany(WorkflowRun::class);
    }
}
