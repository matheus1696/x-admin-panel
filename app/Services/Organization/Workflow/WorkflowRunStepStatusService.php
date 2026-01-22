<?php

namespace App\Services\Organization\Workflow;

use App\Models\Organization\Workflow\WorkflowRunStepStatus;
use Illuminate\Support\Collection;

class WorkflowRunStepStatusService
{
    public function find(int $id): WorkflowRunStepStatus
    {
        return WorkflowRunStepStatus::findOrFail($id);
    }

    public function index(): Collection
    {
        return WorkflowRunStepStatus::orderBy('title')->get();
    }

    public function create(array $data): WorkflowRunStepStatus
    {
        return WorkflowRunStepStatus::create($data);
    }

    public function update(int $id, array $data): WorkflowRunStepStatus
    {
        $workflowRunStepStatus = WorkflowRunStepStatus::findOrFail($id);
        $workflowRunStepStatus->update($data);
        return $workflowRunStepStatus;
    }

    public function delete(WorkflowRunStepStatus $workflowRunStepStatus): void
    {
        $workflowRunStepStatus->delete();
    }
}
