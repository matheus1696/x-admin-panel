<?php

namespace App\Services\Administration\Task;

use App\Models\Administration\Task\TaskStatus;
use Illuminate\Support\Collection;

class TaskStatusService
{
    public function find(int $id, ?int $taskHubId = null): TaskStatus
    {
        return TaskStatus::query()
            ->when($taskHubId !== null, fn ($query) => $query->where('task_hub_id', $taskHubId))
            ->findOrFail($id);
    }

    public function index(?int $taskHubId = null, bool $onlyActive = true): Collection
    {
        return TaskStatus::query()
            ->when($taskHubId !== null, fn ($query) => $query->where('task_hub_id', $taskHubId))
            ->when($onlyActive, fn ($query) => $query->where('is_active', true))
            ->orderBy('id')
            ->get();
    }

    public function create(array $data): TaskStatus
    {
        return TaskStatus::create($data);
    }

    public function createForHub(int $taskHubId, array $data): TaskStatus
    {
        $data['task_hub_id'] = $taskHubId;

        return TaskStatus::create($data);
    }

    public function update(int $id, array $data, ?int $taskHubId = null): TaskStatus
    {
        $taskStatus = $this->find($id, $taskHubId);
        $taskStatus->update($data);
        return $taskStatus;
    }

    public function setDefaultForHub(int $taskHubId, int $statusId): TaskStatus
    {
        TaskStatus::query()
            ->where('task_hub_id', $taskHubId)
            ->update(['is_default' => false]);

        $status = $this->find($statusId, $taskHubId);
        $status->update([
            'is_default' => true,
            'is_active' => true,
        ]);

        return $status;
    }

    public function delete(TaskStatus $taskStatus): void
    {
        $taskStatus->delete();
    }
}
