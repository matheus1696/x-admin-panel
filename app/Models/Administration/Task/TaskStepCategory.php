<?php

namespace App\Models\Administration\Task;

use App\Models\Task\TaskStep;
use Illuminate\Database\Eloquent\Model;

class TaskStepCategory extends Model
{
    protected $fillable = [
        'title',
        'description',
        'is_default',
        'is_active',
    ];

    public function taskStep()
    {
        return $this->hasMany(TaskStep::class);
    }

    public static function default()
    {
        return static::where('is_default', true)->first();
    }
}
