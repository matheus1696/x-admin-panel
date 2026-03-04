<?php

namespace App\Validation\Task;

use Illuminate\Validation\Rule;

class TaskCategoryRules
{
    public static function store(int $taskHubId, ?int $ignoreId = null): array
    {
        $titleRule = Rule::unique('task_categories', 'title')
            ->where(fn ($query) => $query->where('task_hub_id', $taskHubId));

        if ($ignoreId !== null) {
            $titleRule = $titleRule->ignore($ignoreId);
        }

        return [
            'taskHubCategoryTitle' => ['required', 'string', 'min:2', 'max:255', $titleRule],
            'taskHubCategoryDescription' => 'nullable|string|max:1000',
        ];
    }
}
