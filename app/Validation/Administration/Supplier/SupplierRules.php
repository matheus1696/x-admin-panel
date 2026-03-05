<?php

namespace App\Validation\Administration\Supplier;

use Illuminate\Validation\Rule;

class SupplierRules
{
    public static function store(?int $stateId = null): array
    {
        return [
            'title' => ['required', 'string', 'max:255', 'min:3', 'unique:suppliers,title'],
            'trade_name' => ['nullable', 'string', 'max:255'],
            'document' => ['nullable', 'string', 'max:25', 'formato_cpf_ou_cnpj', 'cpf_ou_cnpj', 'unique:suppliers,document'],
            'email' => ['nullable', 'email:rfc,dns', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'phone_secondary' => ['nullable', 'string', 'max:20'],
            'address_street' => ['nullable', 'string', 'max:255'],
            'address_number' => ['nullable', 'string', 'max:30'],
            'address_district' => ['nullable', 'string', 'max:255'],
            'state_id' => ['nullable', 'integer', Rule::exists('region_states', 'id')],
            'city_id' => ['nullable', 'integer', $stateId
                ? Rule::exists('region_cities', 'id')->where(fn ($query) => $query->where('state_id', $stateId))
                : Rule::exists('region_cities', 'id')],
            'address_zipcode' => ['nullable', 'string', 'max:12'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public static function update(int $supplierId, ?int $stateId = null): array
    {
        return [
            'title' => ['required', 'string', 'max:255', 'min:3', Rule::unique('suppliers', 'title')->ignore($supplierId)],
            'trade_name' => ['nullable', 'string', 'max:255'],
            'document' => ['nullable', 'string', 'max:25', 'formato_cpf_ou_cnpj', 'cpf_ou_cnpj', Rule::unique('suppliers', 'document')->ignore($supplierId)],
            'email' => ['nullable', 'email:rfc,dns', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'phone_secondary' => ['nullable', 'string', 'max:20'],
            'address_street' => ['nullable', 'string', 'max:255'],
            'address_number' => ['nullable', 'string', 'max:30'],
            'address_district' => ['nullable', 'string', 'max:255'],
            'state_id' => ['nullable', 'integer', Rule::exists('region_states', 'id')],
            'city_id' => ['nullable', 'integer', $stateId
                ? Rule::exists('region_cities', 'id')->where(fn ($query) => $query->where('state_id', $stateId))
                : Rule::exists('region_cities', 'id')],
            'address_zipcode' => ['nullable', 'string', 'max:12'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
