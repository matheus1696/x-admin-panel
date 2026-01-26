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

    public function RegionCity(){
        return $this->belongsTo(RegionCity::class,'city_id','id');
    }

    public function TypeEstablishment(){
        return $this->belongsTo(EstablishmentType::class,'type_establishment_id','id');
    }

    public function FinancialBlock(){
        return $this->belongsTo(FinancialBlock::class,'financial_block_id','id');
    }

    public function departments()
    {
        return $this->hasMany(Department::class, 'establishment_id');
    }

    public function mainDepartment()
    {
        return $this->hasOne(Department::class, 'establishment_id')
            ->where('type_contact', 'Main');
    }
}
