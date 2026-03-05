<?php

namespace App\Validation\Administration\Product;

use Illuminate\Validation\Rule;

class ProductTypeRules
{
    public static function store(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:255', 'unique:product_types,title'],
            'description' => ['nullable', 'string'],
        ];
    }

    public static function update(int $productTypeId): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:255', Rule::unique('product_types', 'title')->ignore($productTypeId)],
            'description' => ['nullable', 'string'],
        ];
    }
}

