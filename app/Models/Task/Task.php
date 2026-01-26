<?php

namespace App\Models\Task;

use App\Models\Administration\Task\TaskStatus;
use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasUuid;

    protected $fillable = [
        'code',
        'title',
        'description',
        'user_id',
        'task_category_id',
        'task_priority_id',
        'task_status_id',
        'started_at',
        'deadline_at',
        'finished_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'deadline_at' => 'datetime',
    ];

    public function taskStatus()
    {
        return $this->belongsTo(TaskStatus::class, 'task_status_id');
    }

    public function taskSteps()
    {
        return $this->hasMany(TaskStep::class, 'task_id')->orderBy('step_order');
    }
}
