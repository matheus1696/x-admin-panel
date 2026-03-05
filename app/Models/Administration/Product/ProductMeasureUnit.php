<?php

namespace App\Models\Administration\Product;

use App\Models\Traits\HasTitleFilter;
use App\Models\Traits\HasUuid;
use App\Models\Traits\HasUuidRouteKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductMeasureUnit extends Model
{
    use HasTitleFilter, HasUuid, HasUuidRouteKey;

    protected $fillable = [
        'uuid',
        'acronym',
        'title',
        'filter',
        'base_quantity',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'default_measure_unit_id');
    }
}
