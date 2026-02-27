<?php

namespace App\Models\Organization\OrganizationChart;

use App\Models\Traits\HasActive;
use App\Models\Traits\HasTitleFilter;
use App\Models\Traits\HasUuid;
use App\Models\Traits\HasUuidRouteKey;
use Illuminate\Database\Eloquent\Model;
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
        'responsible_photo',
        'responsible_name',
        'responsible_contact',
        'responsible_email',
    ];

    public function children(): HasMany
    {
        return $this->hasMany(OrganizationChart::class, 'hierarchy')
            ->where('is_active', true)
            ->orderBy('order');
    }
}
