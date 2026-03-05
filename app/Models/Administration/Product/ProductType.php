<?php

namespace App\Models\Administration\Product;

use App\Models\Traits\HasTitleFilter;
use App\Models\Traits\HasUuid;
use App\Models\Traits\HasUuidRouteKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductType extends Model
{
    use HasTitleFilter, HasUuid, HasUuidRouteKey;

    protected $fillable = [
        'uuid',
        'title',
        'filter',
        'description',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'product_type_id');
    }
}
