<?php

namespace App\Models\Organization\Workflow;

use App\Models\Administration\User\User;
use App\Models\Organization\OrganizationChart\OrganizationChart;
use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class WorkflowRunStep extends Model
{
    use HasUuid;

    protected $fillable = [
        'workflow_run_id',
        'workflow_step_id',
        'step_order',
        'workflow_run_step_status_id',
        'started_at',
        'finished_at',
        'deadline_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'deadline_at' => 'datetime',
    ];

    public function workflowRun()
    {
        return $this->belongsTo(WorkflowRun::class, 'workflow_run_id');
    }

    public function workflowStep(){
        return $this->belongsTo(WorkflowStep::class, 'workflow_step_id');
    }

    public function workflowRunStepStatus(){
        return $this->belongsTo(WorkflowRunStepStatus::class, 'workflow_run_step_status_id');
    }

    public function users(){
        return $this->belongsToMany(User::class, 'workflow_run_step_users')->withTimestamps();
    }

    public function organizationUnits(){
        return $this->belongsToMany(OrganizationChart::class, 'workflow_run_step_organization_units')->withTimestamps();
    }
}