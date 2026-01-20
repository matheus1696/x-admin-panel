<?php

namespace App\Services\Configuration\Establishment\Establishment;

use App\Models\Manage\Company\Department;
use Illuminate\Support\Collection;

class DepartmentService
{
    public function find(int $id): Department
    {
        return Department::findOrFail($id);
    }

    public function index(int $establishmentId): Collection
    {
        return Department::where('establishment_id', $establishmentId)->orderBy('title')->get();
    }

    public function store(array $data): void
    {
        Department::create($data);
    }

    public function update(int $id, array $data): void
    {
        $department = Department::findOrFail($id);
        $department->update($data);
    }

}