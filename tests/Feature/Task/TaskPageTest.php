<?php

use App\Livewire\Task\TaskPage;
use App\Models\Administration\Task\TaskStepStatus;
use App\Models\Administration\User\User;
use App\Models\Task\Task;
use App\Models\Task\TaskActivity;
use App\Models\Task\TaskHub;
use App\Models\Task\TaskStep;
use Livewire\Livewire;

function createTaskHubForTaskPage(User $owner, string $title, string $acronym): TaskHub
{
    return TaskHub::create([
        'title' => $title,
        'acronym' => $acronym,
        'owner_id' => $owner->id,
    ]);
}

test('moves a step to another kanban column from task page', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $hub = createTaskHubForTaskPage($user, 'Hub Step Kanban', 'HUBS');

    $todo = TaskStepStatus::create(['title' => 'Pendente']);
    $doing = TaskStepStatus::create(['title' => 'Em andamento']);

    $task = Task::create([
        'task_hub_id' => $hub->id,
        'title' => 'Task Base',
    ]);

    $stepA = TaskStep::create([
        'task_id' => $task->id,
        'task_hub_id' => $hub->id,
        'title' => 'Etapa A',
        'task_status_id' => $todo->id,
        'kanban_order' => 1,
    ]);

    $stepB = TaskStep::create([
        'task_id' => $task->id,
        'task_hub_id' => $hub->id,
        'title' => 'Etapa B',
        'task_status_id' => $doing->id,
        'kanban_order' => 1,
    ]);

    Livewire::test(TaskPage::class, ['uuid' => $hub->uuid])
        ->call('reorderStepKanbanCard', $stepA->id, $todo->id, $doing->id, [$stepB->id, $stepA->id]);

    $stepA->refresh();
    $stepB->refresh();

    expect($stepA->task_status_id)->toBe($doing->id);
    expect($stepB->kanban_order)->toBe(1);
    expect($stepA->kanban_order)->toBe(2);
});

test('reorders steps inside the same kanban column from task page', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $hub = createTaskHubForTaskPage($user, 'Hub Step Reorder', 'HUBR');

    $todo = TaskStepStatus::create(['title' => 'Pendente']);

    $task = Task::create([
        'task_hub_id' => $hub->id,
        'title' => 'Task Base',
    ]);

    $stepA = TaskStep::create([
        'task_id' => $task->id,
        'task_hub_id' => $hub->id,
        'title' => 'Etapa A',
        'task_status_id' => $todo->id,
        'kanban_order' => 1,
    ]);

    $stepB = TaskStep::create([
        'task_id' => $task->id,
        'task_hub_id' => $hub->id,
        'title' => 'Etapa B',
        'task_status_id' => $todo->id,
        'kanban_order' => 2,
    ]);

    Livewire::test(TaskPage::class, ['uuid' => $hub->uuid])
        ->call('reorderStepKanbanCard', $stepB->id, $todo->id, $todo->id, [$stepB->id, $stepA->id]);

    $stepA->refresh();
    $stepB->refresh();

    expect($stepB->kanban_order)->toBe(1);
    expect($stepA->kanban_order)->toBe(2);
});

test('requires comment when completing a step from task page kanban', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $hub = createTaskHubForTaskPage($user, 'Hub Step Complete UI', 'HUBC');

    $todo = TaskStepStatus::create(['title' => 'Pendente']);
    $done = TaskStepStatus::create(['title' => 'Concluída']);

    $task = Task::create([
        'task_hub_id' => $hub->id,
        'title' => 'Task Base',
    ]);

    $step = TaskStep::create([
        'task_id' => $task->id,
        'task_hub_id' => $hub->id,
        'title' => 'Etapa Final',
        'task_status_id' => $todo->id,
        'kanban_order' => 1,
    ]);

    Livewire::test(TaskPage::class, ['uuid' => $hub->uuid])
        ->call('requestStepKanbanDrop', $step->id, $todo->id, $done->id, [$step->id])
        ->assertSet('modalKey', 'modal-step-completion-move')
        ->call('confirmStepCompletionMove')
        ->assertHasErrors(['stepCompletionComment']);

    $step->refresh();

    expect($step->task_status_id)->toBe($todo->id);

    Livewire::test(TaskPage::class, ['uuid' => $hub->uuid])
        ->call('requestStepKanbanDrop', $step->id, $todo->id, $done->id, [$step->id])
        ->set('stepCompletionComment', 'Etapa final validada.')
        ->call('confirmStepCompletionMove');

    $step->refresh();

    expect($step->task_status_id)->toBe($done->id);
    expect(TaskActivity::where('task_id', $task->id)->where('type', 'comment')->count())->toBe(1);
    expect(TaskActivity::where('task_id', $task->id)->where('type', 'step_finished_change')->count())->toBe(1);
});

test('requires reason when reopening a step from task page kanban', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $hub = createTaskHubForTaskPage($user, 'Hub Step Reopen UI', 'HUBR');

    $todo = TaskStepStatus::create(['title' => 'Pendente']);
    $done = TaskStepStatus::create(['title' => 'Concluída']);

    $task = Task::create([
        'task_hub_id' => $hub->id,
        'title' => 'Task Base',
    ]);

    $step = TaskStep::create([
        'task_id' => $task->id,
        'task_hub_id' => $hub->id,
        'title' => 'Etapa Reaberta',
        'task_status_id' => $done->id,
        'finished_at' => now(),
        'kanban_order' => 1,
    ]);

    Livewire::test(TaskPage::class, ['uuid' => $hub->uuid])
        ->call('requestStepKanbanDrop', $step->id, $done->id, $todo->id, [$step->id])
        ->assertSet('modalKey', 'modal-step-completion-move')
        ->assertSet('pendingStepMoveReasonType', 'reopen')
        ->call('confirmStepCompletionMove')
        ->assertHasErrors(['stepCompletionComment']);

    $step->refresh();

    expect($step->task_status_id)->toBe($done->id);

    Livewire::test(TaskPage::class, ['uuid' => $hub->uuid])
        ->call('requestStepKanbanDrop', $step->id, $done->id, $todo->id, [$step->id])
        ->set('stepCompletionComment', 'A etapa precisa de ajuste adicional.')
        ->call('confirmStepCompletionMove');

    $step->refresh();

    expect($step->task_status_id)->toBe($todo->id);
    expect($step->finished_at)->toBeNull();
    expect(TaskActivity::where('task_id', $task->id)->where('type', 'comment')->count())->toBe(1);
    expect(TaskActivity::where('task_id', $task->id)->where('type', 'step_reopen_change')->count())->toBe(1);
});

test('requires reason when cancelling a step from task page kanban', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $hub = createTaskHubForTaskPage($user, 'Hub Step Cancel UI', 'HUBX');

    $todo = TaskStepStatus::create(['title' => 'Pendente']);
    $cancelled = TaskStepStatus::create(['title' => 'Cancelada']);

    $task = Task::create([
        'task_hub_id' => $hub->id,
        'title' => 'Task Base',
    ]);

    $step = TaskStep::create([
        'task_id' => $task->id,
        'task_hub_id' => $hub->id,
        'title' => 'Etapa Cancelada',
        'task_status_id' => $todo->id,
        'kanban_order' => 1,
    ]);

    Livewire::test(TaskPage::class, ['uuid' => $hub->uuid])
        ->call('requestStepKanbanDrop', $step->id, $todo->id, $cancelled->id, [$step->id])
        ->assertSet('modalKey', 'modal-step-completion-move')
        ->assertSet('pendingStepMoveReasonType', 'cancellation')
        ->call('confirmStepCompletionMove')
        ->assertHasErrors(['stepCompletionComment']);

    $step->refresh();

    expect($step->task_status_id)->toBe($todo->id);

    Livewire::test(TaskPage::class, ['uuid' => $hub->uuid])
        ->call('requestStepKanbanDrop', $step->id, $todo->id, $cancelled->id, [$step->id])
        ->set('stepCompletionComment', 'Cancelada por redefinição de prioridade.')
        ->call('confirmStepCompletionMove');

    $step->refresh();

    expect($step->task_status_id)->toBe($cancelled->id);
    expect($step->finished_at)->not->toBeNull();
    expect(TaskActivity::where('task_id', $task->id)->where('type', 'comment')->count())->toBe(1);
    expect(TaskActivity::where('task_id', $task->id)->where('type', 'step_cancellation_change')->count())->toBe(1);
});

test('does not allow swapping directly between terminal step statuses from task page kanban', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $hub = createTaskHubForTaskPage($user, 'Hub Step Terminal UI', 'HUBT');

    $done = TaskStepStatus::create(['title' => 'Concluída']);
    $cancelled = TaskStepStatus::create(['title' => 'Cancelada']);

    $task = Task::create([
        'task_hub_id' => $hub->id,
        'title' => 'Task Base',
    ]);

    $step = TaskStep::create([
        'task_id' => $task->id,
        'task_hub_id' => $hub->id,
        'title' => 'Etapa Terminal',
        'task_status_id' => $done->id,
        'finished_at' => now(),
        'kanban_order' => 1,
    ]);

    Livewire::test(TaskPage::class, ['uuid' => $hub->uuid])
        ->call('requestStepKanbanDrop', $step->id, $done->id, $cancelled->id, [$step->id])
        ->assertHasNoErrors()
        ->assertSet('modalKey', null)
        ->assertSet('pendingStepMoveReasonType', null);

    $step->refresh();

    expect($step->task_status_id)->toBe($done->id);
    expect(TaskActivity::where('task_id', $task->id)->count())->toBe(0);
});
