<?php

namespace App\Validation\Configuration\Establishment\Establishment;

use Illuminate\Validation\Rule;

class EstablishmentRules
{
    public static function store(): array
    {
        return [
            'code' => [ 'nullable', 'string', 'max:255', Rule::unique('establishments', 'code'), ],
            'title' => [ 'required', 'string', 'max:255', Rule::unique('establishments', 'title'), ],
            'surname' => [ 'nullable', 'string', 'max:255', Rule::unique('establishments', 'surname'), ],
            'address' => 'required|string|max:255',
            'number'  => 'required|string|max:50',
            'district'=> 'required|string|max:255',
            'city_id' => [ 'required', 'integer', Rule::exists('region_cities', 'id'), ],
            'state_id' => [ 'required', 'integer', Rule::exists('region_states', 'id'), ],
            'latitude'  => 'nullable|string|max:50',
            'longitude' => 'nullable|string|max:50',
            'type_establishment_id' => [ 'required', 'integer', Rule::exists('establishment_types', 'id'), ],
            'financial_block_id' => ['required', 'integer', Rule::exists('financial_blocks', 'id'), ],
            'description' => 'nullable|string',
        ];
    }

    public static function update(int $establishmentId): array
    {
        return [
            'code' => [ 'nullable', 'string', 'max:255', Rule::unique('establishments', 'code')->ignore($establishmentId), ],
            'title' => [ 'required', 'string', 'max:255', Rule::unique('establishments', 'title')->ignore($establishmentId), ],
            'surname' => [ 'nullable', 'string', 'max:255', Rule::unique('establishments', 'surname')->ignore($establishmentId), ],
            'address' => 'required|string|max:255',
            'number'  => 'required|string|max:50',
            'district'=> 'required|string|max:255',
            'city_id' => [ 'required', 'integer', Rule::exists('region_cities', 'id'), ],
            'state_id' => [ 'required', 'integer', Rule::exists('region_states', 'id'), ],
            'latitude'  => 'nullable|string|max:50',
            'longitude' => 'nullable|string|max:50',
            'type_establishment_id' => [ 'required', 'integer', Rule::exists('establishment_types', 'id'), ],
            'financial_block_id' => [ 'required', 'integer', Rule::exists('financial_blocks', 'id'), ],
            'description' => 'nullable|string',
        ];
    }
}
