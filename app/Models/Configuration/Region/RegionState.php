<?php

namespace App\Models\Configuration\Region;

use App\Models\Traits\HasActive;
use App\Models\Traits\HasTitleFilter;
use App\Models\Traits\HasUuid;
use App\Models\Traits\HasUuidRouteKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegionState extends Model
{
    use HasFactory, HasActive, HasTitleFilter, HasUuid, HasUuidRouteKey;

    protected $table = 'region_states';

    protected $fillable = [
        'acronym',
        'code_uf',
        'code_ddd',
        'title',
        'filter',
        'is_active',
        'country_id',
    ];

    public function regionCountry(): BelongsTo
    {
        return $this->belongsTo(RegionCountry::class, 'country_id');
    }
}
