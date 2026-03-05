<?php

namespace App\Models\Administration\Product;

use App\Models\Traits\HasTitleFilter;
use App\Models\Traits\HasUuid;
use App\Models\Traits\HasUuidRouteKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasTitleFilter, HasUuid, HasUuidRouteKey;

    protected $fillable = [
        'uuid',
        'code',
        'sku',
        'title',
        'filter',
        'nature',
        'product_department_id',
        'product_type_id',
        'default_measure_unit_id',
        'description',
    ];

    public function type(): BelongsTo
    {
        return $this->belongsTo(ProductType::class, 'product_type_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(ProductDepartment::class, 'product_department_id');
    }

    public function defaultMeasureUnit(): BelongsTo
    {
        return $this->belongsTo(ProductMeasureUnit::class, 'default_measure_unit_id');
    }
}
