<?php

namespace App\Services\Administration\Product;

use App\Models\Administration\Product\ProductMeasureUnit;
use Illuminate\Support\Collection;

class ProductMeasureUnitService
{
    public function find(int $id): ProductMeasureUnit
    {
        return ProductMeasureUnit::query()->findOrFail($id);
    }

    public function index(): Collection
    {
        return ProductMeasureUnit::query()
            ->orderBy('title')
            ->get();
    }

    public function create(array $data): ProductMeasureUnit
    {
        return ProductMeasureUnit::query()->create($data);
    }

    public function update(int $id, array $data): ProductMeasureUnit
    {
        $measureUnit = ProductMeasureUnit::query()->findOrFail($id);
        $measureUnit->update($data);

        return $measureUnit;
    }
}

