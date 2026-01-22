<?php

namespace App\Models\Organization\Workflow;

use Illuminate\Database\Eloquent\Model;

class WorkflowRunStatus extends Model
{
    protected $fillable = [
        'name',
        'code',
        'color',
    ];

    public function workflowRun()
    {
        return $this->hasMany(WorkflowRun::class);
    }
}
