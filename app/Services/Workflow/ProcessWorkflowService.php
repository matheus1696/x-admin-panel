<?php

namespace App\Services\Workflow;

use App\Models\Workflow\ProcessWorkflow;

class ProcessWorkflowService
{
    public function create(array $data): ProcessWorkflow
    {
        return ProcessWorkflow::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
        ]);
    }

    public function update(int $id, array $data): ProcessWorkflow
    {
        $workflow = ProcessWorkflow::findOrFail($id);

        $workflow->update([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
        ]);

        return $workflow;
    }

    public function status(int $id): ProcessWorkflow
    {
        $workflow = ProcessWorkflow::findOrFail($id);
        return $workflow->toggleStatus();
    }

    public function delete(ProcessWorkflow $workflow): void
    {
        $workflow->delete();
    }
}
