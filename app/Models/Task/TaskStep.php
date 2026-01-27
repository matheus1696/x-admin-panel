<?php

namespace App\Models\Task;

use App\Models\Administration\Task\TaskCategory;
use App\Models\Administration\Task\TaskPriority;
use App\Models\Administration\Task\TaskStepStatus;
use App\Models\Administration\User\User;
use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class TaskStep extends Model
{
    use HasUuid;

    protected $fillable = [
        'task_id',
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

    public function task(){
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function taskStepStatus(){
        return $this->belongsTo(TaskStepStatus::class, 'task_status_id');
    }

    public function taskPriority(){
        return $this->belongsTo(TaskPriority::class, 'task_priority_id');
    }

    public function taskCategory(){
        return $this->belongsTo(TaskCategory::class, 'task_category_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    protected static function booted()
    {
        static::created(function ($task) {
            $task->update([
                'code' => 'TK' . str_pad($task->id, 5, '0', STR_PAD_LEFT),
            ]);
        });
    }
}