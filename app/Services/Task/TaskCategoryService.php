<?php

namespace App\Services\Task;

use App\Models\Administration\Task\TaskCategory;
use App\Models\Task\TaskHub;
use Illuminate\Support\Collection;

class TaskCategoryService
{
    public function visibleForHub(int $taskHubId, bool $onlyActive = false): Collection
    {
        return TaskCategory::query()
            ->when($onlyActive, fn ($query) => $query->where('is_active', true))
            ->where('task_hub_id', $taskHubId)
            ->orderBy('title')
            ->get();
    }

    public function localForHub(int $taskHubId): Collection
    {
        return TaskCategory::query()
            ->where('task_hub_id', $taskHubId)
            ->orderBy('title')
            ->get();
    }

    public function createForHub(int $taskHubId, int $actorId, array $data): ?TaskCategory
    {
        if (! $this->canManage($taskHubId, $actorId)) {
            return null;
        }

        return TaskCategory::create([
            'task_hub_id' => $taskHubId,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'is_active' => true,
            'is_default' => false,
        ]);
    }

    public function updateForHub(int $taskHubId, int $actorId, int $categoryId, array $data): bool
    {
        if (! $this->canManage($taskHubId, $actorId)) {
            return false;
        }

        $category = TaskCategory::query()
            ->where('task_hub_id', $taskHubId)
            ->findOrFail($categoryId);

        $category->update([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
        ]);

        return true;
    }

    public function toggleStatusForHub(int $taskHubId, int $actorId, int $categoryId): bool
    {
        if (! $this->canManage($taskHubId, $actorId)) {
            return false;
        }

        $category = TaskCategory::query()
            ->where('task_hub_id', $taskHubId)
            ->findOrFail($categoryId);

        $category->update([
            'is_active' => ! $category->is_active,
        ]);

        return true;
    }

    public function canManage(int $taskHubId, int $actorId): bool
    {
        $taskHub = TaskHub::query()->findOrFail($taskHubId);

        return (int) $taskHub->owner_id === $actorId;
    }
}
