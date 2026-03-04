<?php

namespace App\Models\Task;

use App\Models\Administration\Task\TaskPriority;
use App\Models\Administration\Task\TaskStepCategory;
use App\Models\Administration\Task\TaskStepStatus;
use App\Models\Administration\User\User;
use App\Models\Organization\OrganizationChart\OrganizationChart;
use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskStep extends Model
{
    use HasUuid;

    protected $fillable = [
        'task_hub_id',
        'task_id',
        'code',
        'title',
        'description',
        'user_id',
        'organization_id',
        'task_category_id',
        'task_priority_id',
        'task_status_id',
        'workflow_step_order',
        'is_required',
        'allow_parallel',
        'kanban_order',
        'started_at',
        'deadline_at',
        'finished_at',
        'created_user_id',
    ];

    protected $casts = [
        'workflow_step_order' => 'integer',
        'is_required' => 'boolean',
        'allow_parallel' => 'boolean',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'deadline_at' => 'datetime',
    ];

    public function taskHub(): BelongsTo
    {
        return $this->belongsTo(TaskHub::class, 'task_hub_id');
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function stepActivities(): HasMany
    {
        return $this->hasMany(TaskStepActivity::class)->orderBy('created_at', 'desc');
    }

    public function taskPriority(): BelongsTo
    {
        return $this->belongsTo(TaskPriority::class, 'task_priority_id');
    }

    public function taskStepStatus(): BelongsTo
    {
        return $this->belongsTo(TaskStepStatus::class, 'task_status_id');
    }

    public function taskStepCategory(): BelongsTo
    {
        return $this->belongsTo(TaskStepCategory::class, 'task_category_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(OrganizationChart::class, 'organization_id');
    }

    protected static function booted()
    {
        static::created(function ($step) {
            $taskStepCount = $step->taskHub->taskSteps()->count();

            $step->update([
                'code' => $step->taskHub->acronym.str_pad($taskStepCount, 5, '0', STR_PAD_LEFT),
            ]);
        });
    }
}
