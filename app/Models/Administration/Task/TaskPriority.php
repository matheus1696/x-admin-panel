<?php

namespace App\Models\Administration\Task;

use App\Models\Task\Task;
use Illuminate\Database\Eloquent\Model;

class TaskPriority extends Model
{
    protected $fillable = [
        'title',
        'level',
        'is_default',
        'is_active',
    ];

    public function task()
    {
        return $this->hasMany(Task::class);
    }

    public static function default()
    {
        return static::where('is_default', true)->first();
    }
}
