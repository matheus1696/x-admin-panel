<?php

namespace App\Models\Organization\Workflow;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WorkflowStep extends Model
{
    //
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

    //Criação do Filter
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;
        $this->attributes['filter'] = Str::ascii(strtolower($value));
    }
}
