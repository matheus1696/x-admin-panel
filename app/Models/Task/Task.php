<?php

namespace App\Models\Task;

use App\Models\Administration\Task\TaskCategory;
use App\Models\Administration\Task\TaskPriority;
use App\Models\Administration\Task\TaskStatus;
use App\Models\Administration\User\User;
use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasUuid;

    protected $fillable = [
        'task_hub_id',
        'uuid',
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
        'created_user_id'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'deadline_at' => 'datetime',
    ];

    public function taskHub(){
        return $this->belongsTo(TaskHub::class, 'task_hub_id');
    }

    public function taskActivities()
    {
        return $this->hasMany(TaskActivity::class)->orderBy('created_at','desc')->get();
    }

    public function taskStatus()
    {
        return $this->belongsTo(TaskStatus::class, 'task_status_id');
    }

    public function taskCategory(){
        return $this->belongsTo(TaskCategory::class, 'task_category_id');
    }

    public function taskPriority(){
        return $this->belongsTo(TaskPriority::class, 'task_priority_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function taskSteps()
    {
        return $this->hasMany(TaskStep::class, 'task_id')->orderBy('code');
    }

    public function taskStepsFinished()
    {
        return $this->hasMany(TaskStep::class, 'task_id')->where('finished_at', '!=', null)->orderBy('code');
    }

    protected static function booted()
    {
        static::created(function ($task) {
            // Conta quantas tarefas existem neste taskHub (incluindo a atual)
            $taskCount = $task->taskHub->tasks()->count();
            
            $task->update([
                'code' => $task->taskHub->acronym . str_pad($taskCount, 5, '0', STR_PAD_LEFT),
            ]);
        });
    }
    
}
