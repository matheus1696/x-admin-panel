<?php

namespace App\Models\Administration\Task;

use App\Models\Task\TaskStep;
use App\Models\Task\TaskHub;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class TaskStepStatus extends Model
{
    protected $fillable = [
        'task_hub_id',
        'title',
        'color',
        'color_code_tailwind',
        'is_default',
        'is_active',
    ];

    public function taskHub(): BelongsTo
    {
        return $this->belongsTo(TaskHub::class, 'task_hub_id');
    }

    public function taskStep(): HasMany
    {
        return $this->hasMany(TaskStep::class);
    }

    public static function default(?int $taskHubId = null): ?static
    {
        return static::query()
            ->when($taskHubId !== null, fn ($query) => $query->where('task_hub_id', $taskHubId))
            ->where('is_default', true)
            ->first();
    }
}
