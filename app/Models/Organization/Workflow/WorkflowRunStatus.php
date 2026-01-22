<?php

namespace App\Models\Organization\Workflow;

use Illuminate\Database\Eloquent\Model;

class WorkflowRunStatus extends Model
{
    protected $fillable = [
        'title',
        'color',
    ];

    public function workflowRun()
    {
        return $this->hasMany(WorkflowRun::class);
    }
}
