<?php

namespace App\Services\Organization\Workflow;

use App\Models\Organization\Workflow\Workflow;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class WorkflowService
{
    public function find(int $id): Workflow
    {
        return Workflow::findOrFail($id);
    }

    public function index(array $filters): LengthAwarePaginator
    {
        $query = Workflow::query();

        if ($filters['title']) {
            $query->where('filter', 'like', '%' . strtolower($filters['title']) . '%');
        }

        if ($filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('title')->paginate($filters['perPage']);
    }

    public function create(array $data): Workflow
    {
        return Workflow::create($data);
    }

    public function update(int $id, array $data): Workflow
    {
        $workflow = Workflow::findOrFail($id);
        $workflow->update($data);
        return $workflow;
    }

    public function status(int $id): Workflow
    {
        $workflow = Workflow::findOrFail($id);
        return $workflow->toggleStatus();
    }

    public function delete(Workflow $workflow): void
    {
        $workflow->delete();
    }
}
