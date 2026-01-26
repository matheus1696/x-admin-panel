<?php

namespace App\Models\Administration\Task;

use App\Models\Task\Task;
use Illuminate\Database\Eloquent\Model;

class TaskStatus extends Model
{
    protected $fillable = [
        'title',
        'color',
        'is_default',
        'is_active',
    ];

    public function task()
    {
        return $this->hasMany(Task::class);
    }
}
