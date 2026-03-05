<?php

namespace App\Validation\Administration\Product;

use Illuminate\Validation\Rule;

class ProductMeasureUnitRules
{
    public static function store(): array
    {
        return [
            'acronym' => ['required', 'string', 'max:50', 'unique:product_measure_units,acronym'],
            'title' => ['required', 'string', 'min:3', 'max:255', 'unique:product_measure_units,title'],
            'base_quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public static function update(int $measureUnitId): array
    {
        return [
            'acronym' => ['required', 'string', 'max:50', Rule::unique('product_measure_units', 'acronym')->ignore($measureUnitId)],
            'title' => ['required', 'string', 'min:3', 'max:255', Rule::unique('product_measure_units', 'title')->ignore($measureUnitId)],
            'base_quantity' => ['required', 'integer', 'min:1'],
        ];
    }
}

