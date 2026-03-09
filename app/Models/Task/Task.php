<?php

namespace App\Models\Task;

use App\Models\Administration\Task\TaskCategory;
use App\Models\Administration\Task\TaskPriority;
use App\Models\Administration\Task\TaskStatus;
use App\Models\Administration\User\User;
use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'kanban_order',
        'started_at',
        'deadline_at',
        'finished_at',
        'created_user_id',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'deadline_at' => 'datetime',
    ];

    public function taskHub(): BelongsTo
    {
        return $this->belongsTo(TaskHub::class, 'task_hub_id');
    }

    public function taskActivities(): HasMany
    {
        return $this->hasMany(TaskActivity::class)->orderBy('created_at', 'desc');
    }

    public function taskStatus(): BelongsTo
    {
        return $this->belongsTo(TaskStatus::class, 'task_status_id');
    }

    public function taskCategory(): BelongsTo
    {
        return $this->belongsTo(TaskCategory::class, 'task_category_id');
    }

    public function taskPriority(): BelongsTo
    {
        return $this->belongsTo(TaskPriority::class, 'task_priority_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function taskSteps(): HasMany
    {
        return $this->hasMany(TaskStep::class, 'task_id')->orderBy('code');
    }

    public function taskStepsFinished(): HasMany
    {
        return $this->hasMany(TaskStep::class, 'task_id')->where('finished_at', '!=', null)->orderBy('code');
    }

    protected static function booted()
    {
        static::created(function ($task) {
            $task->update([
                'code' => $task->taskHub->acronym.str_pad((string) $task->id, 7, '0', STR_PAD_LEFT),
            ]);
        });
    }
}
