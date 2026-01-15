<?php

namespace App\Models\Workflow;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Workflow extends Model
{
    //
    protected $fillable = [
        'title',
        'filter',
        'description',
        'status',
    ];

    public function workflowStage(){
        return $this->hasMany(WorkflowStage::class)->orderBy('order');
    }

    public function toggleStatus(): self
    {
        $this->update(['status' => !$this->status]);
        return $this;
    }

    //Criação do Filter
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;
        $this->attributes['filter'] = Str::ascii(strtolower($value));
    }
}
