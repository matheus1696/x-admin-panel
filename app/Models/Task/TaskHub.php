<?php

namespace App\Models\Task;

use App\Models\Traits\HasTitleFilter;
use App\Models\Traits\HasUuid;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class TaskHub extends Model
{
    use HasUuid, HasTitleFilter;
    //
    protected $fillable = [
        'uuid',
        'acronym',
        'title',
        'filter',
        'description',
        'owner_id',
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class, 'task_hub_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members()
    {
        return $this->hasMany(TaskHubMember::class);
    }
}
