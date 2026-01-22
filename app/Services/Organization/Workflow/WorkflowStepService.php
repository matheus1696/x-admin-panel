<?php

namespace App\Services\Organization\Workflow;

use App\Models\Organization\Workflow\Workflow;
use App\Models\Organization\Workflow\WorkflowStep;

class WorkflowStepService
{
    public function find(int $id): WorkflowStep
    {
        return WorkflowStep::findOrFail($id);
    }

    public function listByWorkflow(int $workflowId)
    {
        return WorkflowStep::where('workflow_id', $workflowId)
            ->orderBy('step_order')
            ->get();
    }

    public function create(array $data): WorkflowStep
    {
        $workflowStep = WorkflowStep::create($data);
        Workflow::find($data['workflow_id'])->increment('total_estimated_days', $data['deadline_days'] ?? 0);
        return $workflowStep;
    }

    public function update(int $id, array $data): WorkflowStep
    {
        $workflowStep = WorkflowStep::findOrFail($id);

        $diff = ($data['deadline_days'] ?? 0) - $workflowStep->deadline_days;

        if ($diff !== 0) {
            Workflow::where('id', $workflowStep->workflow_id)
                ->increment('total_estimated_days', $diff);
        }

        $workflowStep->update($data);
        return $workflowStep;
    }

    public function order(int $id): void
    {
        $workflowStep = WorkflowStep::find($id);
        if ($workflowStep->step_order > 1) {
            $workflowStep->step_order -= 1;
            $workflowStep->save();
        }

        $workflowSteps = WorkflowStep::where('workflow_id', $workflowStep->workflow_id)->get();

        foreach ($workflowSteps as $item) {
            if ($item->id != $workflowStep->id && $item->step_order >= $workflowStep->step_order && $item->step_order < $workflowStep->step_order + 1) {
                $item->step_order += 1;
                $item->save();
            }
        }
    }

    public function delete(WorkflowStep $workflowStep): void
    {
        Workflow::whereKey($workflowStep->workflow_id)->decrement('total_estimated_days', $workflowStep->deadline_days);

        $workflowStep->delete();
    }
}
