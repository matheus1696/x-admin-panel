<?php

namespace App\Models\Task;

use App\Models\Administration\User\User;
use App\Models\Administration\Task\TaskCategory;
use App\Models\Administration\Task\TaskStatus;
use App\Models\Administration\Task\TaskStepStatus;
use App\Models\Organization\OrganizationChart\OrganizationChart;
use App\Models\Traits\HasTitleFilter;
use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskHub extends Model
{
    use HasUuid, HasTitleFilter;

    protected $fillable = [
        'uuid',
        'acronym',
        'title',
        'filter',
        'description',
        'owner_id',
    ];

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'task_hub_id');
    }

    public function taskCategories(): HasMany
    {
        return $this->hasMany(TaskCategory::class, 'task_hub_id');
    }

    public function taskStatuses(): HasMany
    {
        return $this->hasMany(TaskStatus::class, 'task_hub_id');
    }

    public function taskStepStatuses(): HasMany
    {
        return $this->hasMany(TaskStepStatus::class, 'task_hub_id');
    }

    public function taskSteps(): HasMany
    {
        return $this->hasMany(TaskStep::class, 'task_hub_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(TaskHubMember::class);
    }

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(OrganizationChart::class, 'task_hub_organization', 'task_hub_id', 'organization_chart_id')
            ->withTimestamps();
    }
}
