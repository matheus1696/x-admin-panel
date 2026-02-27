<?php

namespace App\Models\Configuration\Establishment\Establishment;

use App\Models\Configuration\Establishment\EstablishmentType\EstablishmentType;
use App\Models\Configuration\FinancialBlock\FinancialBlock;
use App\Models\Configuration\Region\RegionCity;
use App\Models\Traits\HasActive;
use App\Models\Traits\HasTitleFilter;
use App\Models\Traits\HasUuid;
use App\Models\Traits\HasUuidRouteKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Establishment extends Model
{
    use HasActive, HasTitleFilter, HasUuid, HasUuidRouteKey;

    protected $fillable = [
        'code',
        'title',
        'surname',
        'filter',
        'address',
        'number',
        'district',
        'city_id',
        'state_id',
        'latitude',
        'longitude',
        'type_establishment_id',
        'financial_block_id',
        'is_active',
    ];

    public function regionCity(): BelongsTo
    {
        return $this->belongsTo(RegionCity::class, 'city_id');
    }

    public function typeEstablishment(): BelongsTo
    {
        return $this->belongsTo(EstablishmentType::class, 'type_establishment_id');
    }

    public function financialBlock(): BelongsTo
    {
        return $this->belongsTo(FinancialBlock::class, 'financial_block_id');
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class, 'establishment_id');
    }

    public function mainDepartment(): HasOne
    {
        return $this->hasOne(Department::class, 'establishment_id')
            ->where('type_contact', 'Main');
    }
}
