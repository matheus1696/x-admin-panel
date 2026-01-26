<?php

namespace App\Models\Administration\Task;

use App\Models\Task\TaskStep;
use Illuminate\Database\Eloquent\Model;


class TaskStepStatus extends Model
{
    protected $fillable = [
        'title',
        'color',
        'is_default',
        'is_active',
    ];

    public function taskStep()
    {
        return $this->hasMany(TaskStep::class);
    }
}
