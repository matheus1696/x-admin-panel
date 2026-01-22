<?php

namespace App\Models\Organization\Workflow;

use Illuminate\Database\Eloquent\Model;

class WorkflowRunStepOrganizationUnit extends Model
{
    
    protected $fillable = [
        'workflow_run_step_id',
        'organization_chart_id',
    ];

    public $timestamps = true;
}
