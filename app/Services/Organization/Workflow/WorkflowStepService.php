<?php

namespace App\Services\Organization\Workflow;

use App\Models\Organization\Workflow\Workflow;
use App\Models\Organization\Workflow\WorkflowStep;

class WorkflowStepService
{
    public function create(array $data): WorkflowStep
    {
        $workflowStep = WorkflowStep::create([
            'workflow_id' => $data['workflow_id'],
            'title' => $data['title'],
            'deadline_days' => $data['deadline_days'] ?? null,
            'order' => $data['order'],
        ]);

        Workflow::find($data['workflow_id'])->increment('days', $data['deadline_days'] ?? 0);

        return $workflowStep;
    }

    public function update(int $id, array $data): WorkflowStep
    {
        $workflowStep = WorkflowStep::findOrFail($id);

        if ($workflowStep->deadline_days > $data['deadline_days']) {
            Workflow::find($workflowStep->workflow_id)->decrement('days', $workflowStep->deadline_days - $data['deadline_days']);
        } else {
            Workflow::find($workflowStep->workflow_id)->increment('days', $data['deadline_days'] - $workflowStep->deadline_days);
        }

        $workflowStep->update([
            'title' => $data['title'],
            'deadline_days' => $data['deadline_days'] ?? null,
        ]);

        return $workflowStep;
    }

    public function order($data): WorkflowStep
    {
        $workflowSteps = WorkflowStep::where('workflow_id', $data->workflow_id)->get();

        if ($data->order > 1) {
            $data->order -= 1;
            $data->save();
        }

        foreach ($workflowSteps as $item) {
            if ($item->id != $data->id && $item->order >= $data->order && $item->order < $data->order + 1) {
                $item->order += 1;
                $item->save();
            }
        }

        return $data;
    }

    public function status(int $id): WorkflowStep
    {
        $workflowStep = WorkflowStep::findOrFail($id);
        return $workflowStep->toggleStatus();
    }

    public function delete(WorkflowStep $workflowStep): void
    {
        $workflowStep->delete();
    }
}
