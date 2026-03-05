<?php

namespace App\Services\Administration\Product;

use App\Models\Administration\Product\Product;
use Illuminate\Support\Collection;

class ProductService
{
    public function find(int $id): Product
    {
        return Product::query()->findOrFail($id);
    }

    public function index(): Collection
    {
        return Product::query()
            ->with(['type', 'department', 'defaultMeasureUnit'])
            ->orderBy('title')
            ->get();
    }

    public function create(array $data): Product
    {
        return Product::query()->create($data);
    }

    public function update(int $id, array $data): Product
    {
        $product = Product::query()->findOrFail($id);
        $product->update($data);

        return $product;
    }
}
