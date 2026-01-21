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
        'order',
        'deadline_days',
        'required',
    ];

    protected $casts = [
        'required' => 'boolean',
        'deadline_days' => 'integer',
        'order' => 'integer',
    ];

    /**
     * Process workflow
     */
    public function workflow()
    {
        return $this->belongsTo(Workflow::class);
    }
}
