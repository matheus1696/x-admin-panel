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

        if ($filters['workflow']) {
            $query->where('filter', 'like', '%' . strtolower($filters['workflow']) . '%');
        }

        if ($filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('title')->paginate($filters['perPage']);
    }

    public function create(array $data): Workflow
    {
        return Workflow::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
        ]);
    }

    public function update(int $id, array $data): Workflow
    {
        $workflow = Workflow::findOrFail($id);

        $workflow->update([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
        ]);

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
