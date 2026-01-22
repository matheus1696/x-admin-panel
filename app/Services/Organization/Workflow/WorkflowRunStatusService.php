<?php

namespace App\Services\Organization\Workflow;

use App\Models\Organization\Workflow\WorkflowRunStatus;
use Illuminate\Support\Collection;

class WorkflowRunStatusService
{
    public function find(int $id): WorkflowRunStatus
    {
        return WorkflowRunStatus::findOrFail($id);
    }

    public function index(): Collection
    {
        return WorkflowRunStatus::orderBy('title')->get();
    }

    public function create(array $data): WorkflowRunStatus
    {
        return WorkflowRunStatus::create($data);
    }

    public function update(int $id, array $data): WorkflowRunStatus
    {
        $workflowRunStatus = WorkflowRunStatus::findOrFail($id);
        $workflowRunStatus->update($data);
        return $workflowRunStatus;
    }

    public function delete(WorkflowRunStatus $workflowRunStatus): void
    {
        $workflowRunStatus->delete();
    }
}
