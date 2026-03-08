<?php

namespace App\Services\Administration\Task;

use App\Models\Administration\Task\TaskStepStatus;
use Illuminate\Support\Collection;

class TaskStepStatusService
{
    public function find(int $id, ?int $taskHubId = null): TaskStepStatus
    {
        return TaskStepStatus::query()
            ->when($taskHubId !== null, fn ($query) => $query->where('task_hub_id', $taskHubId))
            ->findOrFail($id);
    }

    public function index(?int $taskHubId = null, bool $onlyActive = true): Collection
    {
        return TaskStepStatus::query()
            ->when($taskHubId !== null, fn ($query) => $query->where('task_hub_id', $taskHubId))
            ->when($onlyActive, fn ($query) => $query->where('is_active', true))
            ->orderBy('id')
            ->get();
    }

    public function create(array $data): TaskStepStatus
    {
        return TaskStepStatus::create($data);
    }

    public function createForHub(int $taskHubId, array $data): TaskStepStatus
    {
        $data['task_hub_id'] = $taskHubId;

        return TaskStepStatus::create($data);
    }

    public function update(int $id, array $data, ?int $taskHubId = null): TaskStepStatus
    {
        $taskStepStatus = $this->find($id, $taskHubId);
        $taskStepStatus->update($data);
        return $taskStepStatus;
    }

    public function setDefaultForHub(int $taskHubId, int $statusId): TaskStepStatus
    {
        TaskStepStatus::query()
            ->where('task_hub_id', $taskHubId)
            ->update(['is_default' => false]);

        $status = $this->find($statusId, $taskHubId);
        $status->update([
            'is_default' => true,
            'is_active' => true,
        ]);

        return $status;
    }

    public function delete(TaskStepStatus $taskStepStatus): void
    {
        $taskStepStatus->delete();
    }
}
