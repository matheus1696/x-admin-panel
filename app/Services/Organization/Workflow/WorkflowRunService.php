<?php

namespace App\Services\Organization\Workflow;

use App\Models\Organization\Workflow\WorkflowRun;
use App\Models\Organization\Workflow\WorkflowRunStep;
use App\Models\Organization\Workflow\WorkflowStep;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class WorkflowRunService
{
    public function find(int $id): WorkflowRun
    {
        return WorkflowRun::findOrFail($id);
    }

    public function index(array $filters): LengthAwarePaginator
    {
        $query = WorkflowRun::query();

        // Filtra pelo título
        if ($filters['title']) {
            $query->where('title', 'like', '%' . $filters['title'] . '%');
        }

        // Filtra pelo status
        if ($filters['workflow_run_status_id'] != 'all') {
            $query->where('workflow_run_status_id', $filters['workflow_run_status_id']);
        }

        return $query->orderBy('title')->paginate($filters['perPage']);
    }

    public function create(array $data): WorkflowRun
    {
        $data['workflow_run_status_id'] = 1;
        $workflowRun = WorkflowRun::create($data);
        $workflowSteps = WorkflowStep::where('workflow_id', $data['workflow_id'])
            ->orderBy('step_order')
            ->get();

        $workflowRun->current_workflow_step_id = $workflowSteps->first()->id;
        $workflowRun->save();

        foreach ($workflowSteps as $step) {
            WorkflowRunStep::create([
                'workflow_run_id' => $workflowRun->id,
                'workflow_step_id' => $step->id,
                'step_order' => $step->step_order,
                'workflow_run_step_status_id' => 1,
            ]);
        }

        return $workflowRun;
    }

    public function update(int $id, array $data): WorkflowRun
    {
        $workflowRun = WorkflowRun::findOrFail($id);
        $workflowRun->update($data);
        return $workflowRun;
    }
}
