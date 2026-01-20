<?php

namespace App\Services\Configuration\Establishment\Establishment;

use App\Models\Manage\Company\Establishment;
use Illuminate\Pagination\LengthAwarePaginator;

class EstablishmentService
{
    public function find(int $id): Establishment
    {
        return Establishment::findOrFail($id);
    }

    public function index(array $filters): LengthAwarePaginator
    {
        $query = Establishment::query();

        if ($filters['code']) { $query->where('code', 'like', '%' . strtolower($filters['code']) . '%'); }

        if ($filters['filter']) { $query->where('filter', 'like', '%' . strtolower($filters['filter']) . '%'); }

        if ($filters['status'] !== 'all') { $query->where('status', $filters['status']); }

        switch ($filters['sort']) {
            case 'name_asc':
                $query->orderBy('title', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('title', 'desc');
                break;
        }
        
        return $query->orderBy('title')->paginate($filters['perPage']);
    }

    public function store(array $data): void
    {
        Establishment::create($data);
    }

    public function update(int $id, array $data): void
    {
        $establishment = Establishment::findOrFail($id);
        $establishment->update($data);
    }

    public function status(int $id): Establishment
    {
        $establishment = Establishment::findOrFail($id);
        return $establishment->toggleStatus();
    }

}