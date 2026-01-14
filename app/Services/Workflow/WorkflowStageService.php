<?php

namespace App\Services\Workflow;

use App\Models\Workflow\WorkflowStage;

class WorkflowStageService
{
    public function create(array $data): WorkflowStage
    {
        return WorkflowStage::create([
            'task_type_id' => $data['task_type_id'],
            'title' => $data['title'],
            'deadline_days' => $data['deadline_days'] ?? null,
            'order' => $data['order'],
        ]);
    }

    public function update(int $id, array $data): WorkflowStage
    {
        $workflowStage = WorkflowStage::findOrFail($id);

        $workflowStage->update([
            'title' => $data['title'],
            'deadline_days' => $data['deadline_days'] ?? null,
        ]);

        return $workflowStage;
    }

    public function status(int $id): WorkflowStage
    {
        $workflowStage = WorkflowStage::findOrFail($id);
        return $workflowStage->toggleStatus();
    }

    public function delete(WorkflowStage $workflowStage): void
    {
        $workflowStage->delete();
    }
}
