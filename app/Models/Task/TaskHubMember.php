<?php

namespace App\Models\Task;

use App\Models\Administration\User\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskHubMember extends Model
{
    protected $fillable = [
        'task_hub_id',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function taskHub(): BelongsTo
    {
        return $this->belongsTo(TaskHub::class);
    }
}
