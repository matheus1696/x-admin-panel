<?php

namespace App\Models\Manage\Company;

use App\Models\Configuration\Region\RegionCity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Establishment extends Model
{
    //
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
        'status',
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

    public function toggleStatus(): self
    {
        $this->update(['status' => !$this->status]);
        return $this;
    }

    //Criação do Filter Title
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;
        $this->attributes['filter'] = Str::ascii(strtolower($value));
    }
}
