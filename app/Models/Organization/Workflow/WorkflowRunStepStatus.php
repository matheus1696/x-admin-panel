<?php

namespace App\Models\Organization\Workflow;

use Illuminate\Database\Eloquent\Model;


class WorkflowRunStepStatus extends Model
{
    protected $fillable = [
        'title',
        'code',
        'color',
    ];

    public function workflowRunStep()
    {
        return $this->hasMany(WorkflowRunStep::class);
    }
}
