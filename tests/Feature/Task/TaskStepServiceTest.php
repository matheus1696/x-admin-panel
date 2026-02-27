<?php

use App\Models\Administration\Task\TaskStepStatus;
use App\Models\Administration\User\User;
use App\Models\Task\Task;
use App\Models\Task\TaskHub;
use App\Models\Task\TaskStep;
use App\Models\Task\TaskStepActivity;
use App\Services\Task\TaskService;
use Illuminate\Support\Facades\Auth;

function createTaskHubForTaskStepReason(User $owner, string $title, string $acronym): TaskHub
{
    return TaskHub::create([
        'title' => $title,
        'acronym' => $acronym,
        'owner_id' => $owner->id,
    ]);
}

test('moveKanbanStep stores reason metadata when provided', function () {
    $user = User::factory()->create();
    Auth::login($user);

    $hub = createTaskHubForTaskStepReason($user, 'Hub Step Reason', 'HUBSR');

    $pending = TaskStepStatus::create(['title' => 'Pendente']);
    $done = TaskStepStatus::create(['title' => 'Concluída']);

    $task = Task::create([
        'task_hub_id' => $hub->id,
        'title' => 'Task Step Reason',
    ]);

    $step = TaskStep::create([
        'task_id' => $task->id,
        'task_hub_id' => $hub->id,
        'title' => 'Step Reason',
        'task_status_id' => $pending->id,
        'kanban_order' => 1,
    ]);

    app(TaskService::class)->moveKanbanStep(
        $hub->uuid,
        $step->id,
        $pending->id,
        $done->id,
        [],
        [$step->id],
        'Finalizada com evidÃªncia',
        'completion'
    );

    $activity = TaskStepActivity::where('task_step_id', $step->id)
        ->where('type', 'kanban_move')
        ->first();

    expect($activity)->not->toBeNull();
    expect($activity->meta['reason'])->toBe('Finalizada com evidÃªncia');
    expect($activity->meta['reason_type'])->toBe('completion');
});
