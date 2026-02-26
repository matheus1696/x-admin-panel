<?php

namespace App\Models\Configuration\Region;

use App\Models\Traits\HasActive;
use App\Models\Traits\HasTitleFilter;
use App\Models\Traits\HasUuid;
use App\Models\Traits\HasUuidRouteKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegionCity extends Model
{
    use HasFactory, HasActive, HasTitleFilter, HasUuid, HasUuidRouteKey;

    protected $table = 'region_cities';

    protected $fillable = [
        'code_ibge',
        'title',
        'filter',
        'code_cep',
        'is_active',
        'state_id',
    ];

    public function regionState(): BelongsTo
    {
        return $this->belongsTo(RegionState::class, 'state_id');
    }
}
