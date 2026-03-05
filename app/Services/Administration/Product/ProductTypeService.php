<?php

namespace App\Services\Administration\Product;

use App\Models\Administration\Product\ProductType;
use Illuminate\Support\Collection;

class ProductTypeService
{
    public function find(int $id): ProductType
    {
        return ProductType::query()->findOrFail($id);
    }

    public function index(): Collection
    {
        return ProductType::query()
            ->orderBy('title')
            ->get();
    }

    public function create(array $data): ProductType
    {
        return ProductType::query()->create($data);
    }

    public function update(int $id, array $data): ProductType
    {
        $productType = ProductType::query()->findOrFail($id);
        $productType->update($data);

        return $productType;
    }
}

