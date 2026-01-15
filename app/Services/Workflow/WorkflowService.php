<?php

namespace App\Services\Workflow;

use App\Models\Workflow\Workflow;

class WorkflowService
{
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
