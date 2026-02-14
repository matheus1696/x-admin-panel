<?php

namespace App\Models\Task;

use App\Models\Administration\User\User;
use Illuminate\Database\Eloquent\Model;

class TaskHubMember extends Model
{
    //
    protected $fillable = [
        'task_hub_id',
        'user_id',
        'role',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function taskHub()
    {
        return $this->belongsTo(TaskHub::class);
    }
}
