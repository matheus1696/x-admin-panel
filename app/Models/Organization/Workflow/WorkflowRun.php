<?php

namespace App\Models\Organization\Workflow;

use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class WorkflowRun extends Model
{
    use HasUuid;

    protected $fillable = [
        'workflow_id',
        'workflow_run_status_id',
        'current_workflow_step_id',
        'started_at',
        'finished_at',
        'title',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function workflow()
    {
        return $this->belongsTo(Workflow::class);
    }

    public function currentWorkflowStep()
    {
        return $this->belongsTo(WorkflowStep::class, 'current_workflow_step_id');
    }

    public function workflowRunSteps()
    {
        return $this->hasMany(WorkflowRunStep::class)
            ->orderBy('step_order');
    }

    public function workflowRunStatus()
    {
        return $this->belongsTo(WorkflowRunStatus::class, 'workflow_run_status_id');
    }
}
