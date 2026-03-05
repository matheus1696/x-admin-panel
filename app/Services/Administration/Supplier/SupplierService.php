<?php

namespace App\Services\Administration\Supplier;

use App\Models\Administration\Supplier\Supplier;
use Illuminate\Support\Collection;

class SupplierService
{
    public function find(int $id): Supplier
    {
        return Supplier::query()->findOrFail($id);
    }

    public function index(): Collection
    {
        return Supplier::query()
            ->with(['state', 'city'])
            ->orderByDesc('is_active')
            ->orderBy('title')
            ->get();
    }

    public function create(array $data): Supplier
    {
        return Supplier::query()->create($data);
    }

    public function update(int $id, array $data): Supplier
    {
        $supplier = Supplier::query()->findOrFail($id);
        $supplier->update($data);

        return $supplier;
    }
}
