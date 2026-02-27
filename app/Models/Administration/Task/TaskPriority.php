<?php

namespace App\Models\Administration\Task;

use App\Models\Task\Task;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskPriority extends Model
{
    protected $fillable = [
        'title',
        'level',
        'is_default',
        'is_active',
    ];

    public function task(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public static function default(): ?static
    {
        return static::where('is_default', true)->first();
    }
}
