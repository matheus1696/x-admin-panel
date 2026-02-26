<?php

namespace App\Models\Administration\Task;

use App\Models\Task\TaskStep;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskStepCategory extends Model
{
    protected $fillable = [
        'title',
        'description',
        'is_default',
        'is_active',
    ];

    public function taskStep(): HasMany
    {
        return $this->hasMany(TaskStep::class);
    }

    public static function default(): ?static
    {
        return static::where('is_default', true)->first();
    }
}
