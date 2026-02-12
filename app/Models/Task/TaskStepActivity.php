<?php

namespace App\Models\Task;

use App\Models\Administration\User\User;
use Illuminate\Database\Eloquent\Model;

class TaskStepActivity extends Model
{
    protected $fillable = [
        'task_step_id',
        'user_id',
        'type',
        'description',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
