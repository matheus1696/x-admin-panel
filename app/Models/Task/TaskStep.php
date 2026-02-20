<?php

namespace App\Models\Task;

use App\Models\Administration\Task\TaskPriority;
use App\Models\Administration\Task\TaskStepCategory;
use App\Models\Administration\Task\TaskStepStatus;
use App\Models\Administration\User\User;
use App\Models\Organization\OrganizationChart\OrganizationChart;
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
        'organization_id',
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

    public function stepActivities()
    {
        return $this->hasMany(TaskStepActivity::class)->orderBy('created_at','desc')->get();
    }

    public function task(){
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function taskPriority(){
        return $this->belongsTo(TaskPriority::class, 'task_priority_id');
    }

    public function taskStepStatus(){
        return $this->belongsTo(TaskStepStatus::class, 'task_status_id');
    }

    public function taskStepCategory(){
        return $this->belongsTo(TaskStepCategory::class, 'task_category_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function organization()
    {
        return $this->belongsTo(OrganizationChart::class, 'organization_id');
    }

    protected static function booted()
    {
        static::created(function ($step) {
            $step->update([
                'code' => $step->task->taskHub->acronym . str_pad($step->id, 5, '0', STR_PAD_LEFT),
            ]);
        });

        static::created(function ($step) {
            // Conta quantas tarefas existem neste taskHub (incluindo a atual)
            $taskStepCount = $step->task->taskSteps()->count();
            
            $step->update([
                'code' => $step->task->taskHub->acronym . str_pad($taskStepCount, 5, '0', STR_PAD_LEFT),
            ]);
        });
    }
}