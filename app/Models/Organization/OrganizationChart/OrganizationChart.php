<?php

namespace App\Models\Organization\OrganizationChart;

use App\Models\Administration\User\User;
use App\Models\Traits\HasActive;
use App\Models\Traits\HasTitleFilter;
use App\Models\Traits\HasUuid;
use App\Models\Traits\HasUuidRouteKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrganizationChart extends Model
{
    use HasActive, HasTitleFilter, HasUuid, HasUuidRouteKey;

    protected $fillable = [
        'acronym',
        'title',
        'order',
        'hierarchy',
        'number_hierarchy',
        'is_active',
        'responsible_user_id',
    ];

    public function children(): HasMany
    {
        return $this->hasMany(OrganizationChart::class, 'hierarchy')
            ->where('is_active', true)
            ->orderBy('order');
    }

    public function responsibleUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_user_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'organization_chart_user', 'organization_chart_id', 'user_id')
            ->withTimestamps();
    }
}
