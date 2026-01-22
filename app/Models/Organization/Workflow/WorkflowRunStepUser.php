<?php

namespace App\Models\Organization\Workflow;

use Illuminate\Database\Eloquent\Model;

class WorkflowRunStepUser extends Model
{
    protected $fillable = [
        'workflow_run_step_id',
        'user_id',
    ];

    public $timestamps = true;
}
