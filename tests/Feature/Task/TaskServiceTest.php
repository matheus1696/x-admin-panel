<?php

use App\Models\Administration\User\User;
use App\Models\Administration\Task\TaskStepStatus;
use App\Models\Task\Task;
use App\Models\Task\TaskActivity;
use App\Models\Task\TaskHub;
use App\Models\Task\TaskStep;
use App\Services\Task\TaskService;
use Illuminate\Support\Facades\Auth;

function createTaskHub(User $owner, string $title, string $acronym): TaskHub
{
    return TaskHub::create([
        'title' => $title,
        'acronym' => $acronym,
        'owner_id' => $owner->id,
    ]);
}

test('storeComment updates task updated_at and creates activity', function () {
    $user = User::factory()->create();
    $hub = createTaskHub($user, 'Hub Comentarios', 'HUBC');
    $task = Task::create([
        'task_hub_id' => $hub->id,
        'title' => 'Tarefa Teste',
    ]);

    $task->update(['updated_at' => now()->subDay()]);

    Auth::login($user);

    app(TaskService::class)->storeComment($task->id, ['comment' => 'Teste'], 'comment');

    $task->refresh();

    expect($task->updated_at->greaterThan(now()->subMinute()))->toBeTrue();
    expect(TaskActivity::where('task_id', $task->id)->count())->toBe(1);
});

test('dashboard aggregates task and step metrics for a hub', function () {
    $owner = User::factory()->create();
    $responsible = User::factory()->create();
    $hub = createTaskHub($owner, 'Hub Dashboard', 'HUBD');

    $taskInProgress = Task::create([
        'task_hub_id' => $hub->id,
        'title' => 'Em andamento',
        'user_id' => $responsible->id,
        'started_at' => now()->subHour(),
    ]);

    $taskCompleted = Task::create([
        'task_hub_id' => $hub->id,
        'title' => 'Concluida',
        'user_id' => $responsible->id,
        'finished_at' => now()->subMinute(),
    ]);

    $taskOverdue = Task::create([
        'task_hub_id' => $hub->id,
        'title' => 'Atrasada',
        'deadline_at' => now()->subDay(),
    ]);

    $pending = TaskStepStatus::create(['title' => 'Pendente']);
    $running = TaskStepStatus::create(['title' => 'Em execucao']);

    TaskStep::create([
        'task_id' => $taskInProgress->id,
        'task_hub_id' => $hub->id,
        'title' => 'Etapa 1',
        'task_status_id' => $pending->id,
    ]);

    TaskStep::create([
        'task_id' => $taskInProgress->id,
        'task_hub_id' => $hub->id,
        'title' => 'Etapa 2',
        'task_status_id' => $running->id,
    ]);

    $stats = app(TaskService::class)->dashboard($hub->uuid);

    expect($stats['total'])->toBe(3);
    expect($stats['in_progress'])->toBe(1);
    expect($stats['completed'])->toBe(1);
    expect($stats['overdue'])->toBe(1);
    expect(collect($stats['tasks_by_responsible'])->pluck('total')->sum())->toBe(3);
    expect(collect($stats['tasks_by_step_status'])->pluck('total')->sum())->toBe(2);
});
