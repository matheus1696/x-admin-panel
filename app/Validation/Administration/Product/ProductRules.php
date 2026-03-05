<?php

namespace App\Validation\Administration\Product;

use Illuminate\Validation\Rule;

class ProductRules
{
    public static function store(): array
    {
        return [
            'code' => ['nullable', 'string', 'max:50', 'unique:products,code'],
            'sku' => ['nullable', 'string', 'max:80', 'unique:products,sku'],
            'title' => ['required', 'string', 'max:255', 'min:3', 'unique:products,title'],
            'nature' => ['required', 'in:ASSET,SUPPLY'],
            'product_department_id' => ['nullable', 'integer', 'exists:product_departments,id'],
            'product_type_id' => ['required', 'integer', 'exists:product_types,id'],
            'default_measure_unit_id' => ['nullable', 'integer', 'exists:product_measure_units,id'],
            'description' => ['nullable', 'string'],
        ];
    }

    public static function update(int $productId): array
    {
        return [
            'code' => ['nullable', 'string', 'max:50', Rule::unique('products', 'code')->ignore($productId)],
            'sku' => ['nullable', 'string', 'max:80', Rule::unique('products', 'sku')->ignore($productId)],
            'title' => ['required', 'string', 'max:255', 'min:3', Rule::unique('products', 'title')->ignore($productId)],
            'nature' => ['required', 'in:ASSET,SUPPLY'],
            'product_department_id' => ['nullable', 'integer', 'exists:product_departments,id'],
            'product_type_id' => ['required', 'integer', 'exists:product_types,id'],
            'default_measure_unit_id' => ['nullable', 'integer', 'exists:product_measure_units,id'],
            'description' => ['nullable', 'string'],
        ];
    }
}
