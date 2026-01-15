<?php

namespace App\Services\Workflow;

use App\Models\Workflow\Workflow;
use App\Models\Workflow\WorkflowStage;

class WorkflowStageService
{
    public function create(array $data): WorkflowStage
    {
        $workflowStage = WorkflowStage::create([
            'workflow_id' => $data['workflow_id'],
            'title' => $data['title'],
            'deadline_days' => $data['deadline_days'] ?? null,
            'order' => $data['order'],
        ]);

        Workflow::find($data['workflow_id'])->increment('days', $data['deadline_days'] ?? 0);

        return $workflowStage;
    }

    public function update(int $id, array $data): WorkflowStage
    {
        $workflowStage = WorkflowStage::findOrFail($id);

        if ($workflowStage->deadline_days > $data['deadline_days']) {
            Workflow::find($workflowStage->workflow_id)->decrement('days', $workflowStage->deadline_days - $data['deadline_days']);
        } else {
            Workflow::find($workflowStage->workflow_id)->increment('days', $data['deadline_days'] - $workflowStage->deadline_days);
        }

        $workflowStage->update([
            'title' => $data['title'],
            'deadline_days' => $data['deadline_days'] ?? null,
        ]);

        return $workflowStage;
    }

    public function order($data): WorkflowStage
    {
        $workflowStages = WorkflowStage::where('workflow_id', $data->workflow_id)->get();

        if ($data->order > 1) {
            $data->order -= 1;
            $data->save();
        }

        foreach ($workflowStages as $item) {
            if ($item->id != $data->id && $item->order >= $data->order && $item->order < $data->order + 1) {
                $item->order += 1;
                $item->save();
            }
        }

        return $data;
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
