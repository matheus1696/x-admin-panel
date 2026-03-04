<?php

namespace App\Models\Administration\Task;

use App\Models\Task\Task;
use App\Models\Task\TaskHub;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskCategory extends Model
{
    protected $fillable = [
        'task_hub_id',
        'title',
        'description',
        'is_default',
        'is_active',
    ];

    public function task(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function taskHub(): BelongsTo
    {
        return $this->belongsTo(TaskHub::class, 'task_hub_id');
    }

    public static function defaultForHub(int $taskHubId): ?static
    {
        return static::where('task_hub_id', $taskHubId)
            ->where('is_default', true)
            ->first();
    }
}
