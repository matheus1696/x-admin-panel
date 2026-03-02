<?php

use App\Models\Administration\Task\TaskStatus;
use App\Models\Administration\Task\TaskStepStatus;
use App\Models\Administration\User\User;
use App\Models\Organization\OrganizationChart\OrganizationChart;
use App\Models\Task\Task;
use App\Models\Task\TaskActivity;
use App\Models\Task\TaskHub;
use App\Models\Task\TaskHubMember;
use App\Models\Task\TaskStep;
use App\Models\Task\TaskStepActivity;
use App\Services\Task\TaskService;
use Illuminate\Support\Facades\Auth;

function createTaskHubForTaskService(User $owner, string $title, string $acronym): TaskHub
{
    return TaskHub::create([
        'title' => $title,
        'acronym' => $acronym,
        'owner_id' => $owner->id,
    ]);
}

test('storeComment updates task updated_at and creates activity', function () {
    $user = User::factory()->create();
    $hub = createTaskHubForTaskService($user, 'Hub Comentarios', 'HUBC');
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
    $hub = createTaskHubForTaskService($owner, 'Hub Dashboard', 'HUBD');

    $inProgressStatus = TaskStatus::create(['title' => 'Em andamento']);
    $completedStatus = TaskStatus::create(['title' => 'Concluído']);
    $cancelledStatus = TaskStatus::create(['title' => 'Cancelado']);

    $taskInProgress = Task::create([
        'task_hub_id' => $hub->id,
        'title' => 'Em andamento',
        'user_id' => $responsible->id,
        'task_status_id' => $inProgressStatus->id,
    ]);

    $taskCompleted = Task::create([
        'task_hub_id' => $hub->id,
        'title' => 'Concluida',
        'user_id' => $responsible->id,
        'task_status_id' => $completedStatus->id,
    ]);

    $taskOverdue = Task::create([
        'task_hub_id' => $hub->id,
        'title' => 'Atrasada',
        'task_status_id' => $inProgressStatus->id,
        'deadline_at' => now()->subDay(),
    ]);

    Task::create([
        'task_hub_id' => $hub->id,
        'title' => 'Cancelada',
        'task_status_id' => $cancelledStatus->id,
    ]);

    $pending = TaskStepStatus::create(['title' => 'Pendente']);
    $running = TaskStepStatus::create(['title' => 'Em execucao']);
    TaskStepStatus::create(['title' => 'Concluída']);
    TaskStepStatus::create(['title' => 'Cancelada']);

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

    expect($stats['total'])->toBe(4);
    expect($stats['in_progress'])->toBe(2);
    expect($stats['completed'])->toBe(1);
    expect($stats['overdue'])->toBe(1);
    expect($stats['cancelled'])->toBe(1);
    expect(collect($stats['tasks_by_responsible'])->pluck('total')->sum())->toBe(4);
    expect(collect($stats['tasks_by_step_status'])->pluck('total')->sum())->toBe(2);
    expect($stats['tasks_active_total'])->toBe(2);
    expect($stats['steps_active_total'])->toBe(2);
});

test('userOverview aggregates only hubs visible to the user', function () {
    $viewer = User::factory()->create();
    $owner = User::factory()->create();
    $responsible = User::factory()->create();

    $ownedHub = createTaskHubForTaskService($viewer, 'Hub Próprio', 'HUBP');
    $sharedHub = createTaskHubForTaskService($owner, 'Hub Compartilhado', 'HUBC');
    $hiddenHub = createTaskHubForTaskService($owner, 'Hub Oculto', 'HUBO');

    TaskHubMember::create([
        'task_hub_id' => $sharedHub->id,
        'user_id' => $viewer->id,
    ]);

    $inProgressStatus = TaskStatus::create(['title' => 'Em andamento']);
    $completedStatus = TaskStatus::create(['title' => 'Concluído']);
    $cancelledStatus = TaskStatus::create(['title' => 'Cancelado']);

    $ownedTask = Task::create([
        'task_hub_id' => $ownedHub->id,
        'title' => 'Tarefa Própria',
        'user_id' => $responsible->id,
        'task_status_id' => $inProgressStatus->id,
    ]);

    $sharedTask = Task::create([
        'task_hub_id' => $sharedHub->id,
        'title' => 'Tarefa Atrasada',
        'task_status_id' => $inProgressStatus->id,
        'deadline_at' => now()->subDay(),
    ]);

    Task::create([
        'task_hub_id' => $sharedHub->id,
        'title' => 'Tarefa Concluída',
        'user_id' => $responsible->id,
        'task_status_id' => $completedStatus->id,
    ]);

    $hiddenTask = Task::create([
        'task_hub_id' => $hiddenHub->id,
        'title' => 'Tarefa Oculta',
        'task_status_id' => $cancelledStatus->id,
    ]);

    $pendingStepStatus = TaskStepStatus::create(['title' => 'Pendente']);
    $organization = OrganizationChart::create([
        'title' => 'Setor Financeiro',
        'acronym' => 'FIN',
        'order' => 1,
        'hierarchy' => 0,
        'is_active' => true,
    ]);

    TaskStep::create([
        'task_id' => $sharedTask->id,
        'task_hub_id' => $sharedHub->id,
        'title' => 'Etapa Visível',
        'organization_id' => $organization->id,
        'task_status_id' => $pendingStepStatus->id,
    ]);

    TaskStep::create([
        'task_id' => $hiddenTask->id,
        'task_hub_id' => $hiddenHub->id,
        'title' => 'Etapa Oculta',
        'organization_id' => $organization->id,
        'task_status_id' => $pendingStepStatus->id,
    ]);

    $overview = app(TaskService::class)->userOverview($viewer->id);

    expect($overview['hubs_total'])->toBe(2);
    expect($overview['total'])->toBe(3);
    expect($overview['overdue'])->toBe(1);
    expect(collect($overview['statuses'])->pluck('total')->sum())->toBe(3);
    expect(collect($overview['users'])->pluck('total')->sum())->toBe(3);
    expect(collect($overview['organizations'])->pluck('total')->sum())->toBe(1);
    expect($overview['overdue_tasks'])->toHaveCount(1);
    expect($overview['overdue_tasks'][0]['hub'])->toBe('HUBC');
});

test('moveKanbanTask updates status, ordering, and history', function () {
    $user = User::factory()->create();
    Auth::login($user);

    $hub = createTaskHubForTaskService($user, 'Hub Kanban', 'HUBK');

    $todo = TaskStatus::create([
        'title' => 'Todo',
        'color' => 'gray',
        'color_code_tailwind' => 'bg-gray-100 text-gray-800',
        'is_default' => true,
        'is_active' => true,
    ]);

    $doing = TaskStatus::create([
        'title' => 'Doing',
        'color' => 'blue',
        'color_code_tailwind' => 'bg-blue-100 text-blue-700',
        'is_default' => false,
        'is_active' => true,
    ]);

    $taskA1 = Task::create([
        'task_hub_id' => $hub->id,
        'title' => 'Task A1',
        'task_status_id' => $todo->id,
        'kanban_order' => 1,
    ]);

    $taskA2 = Task::create([
        'task_hub_id' => $hub->id,
        'title' => 'Task A2',
        'task_status_id' => $todo->id,
        'kanban_order' => 2,
    ]);

    $taskB1 = Task::create([
        'task_hub_id' => $hub->id,
        'title' => 'Task B1',
        'task_status_id' => $doing->id,
        'kanban_order' => 1,
    ]);

    app(TaskService::class)->moveKanbanTask(
        $hub->uuid,
        $taskA2->id,
        $todo->id,
        $doing->id,
        [$taskA1->id],
        [$taskB1->id, $taskA2->id],
        null,
        null
    );

    $taskA2->refresh();

    expect($taskA2->task_status_id)->toBe($doing->id);
    expect(
        Task::query()
            ->where('task_status_id', $todo->id)
            ->orderBy('kanban_order')
            ->pluck('id')
            ->all()
    )->toBe([$taskA1->id]);

    expect(
        Task::query()
            ->where('task_status_id', $doing->id)
            ->orderBy('kanban_order')
            ->pluck('id')
            ->all()
    )->toBe([$taskB1->id, $taskA2->id]);

    expect(
        TaskActivity::where('task_id', $taskA2->id)
            ->where('type', 'kanban_move')
            ->count()
    )->toBe(1);
});

test('moveKanbanTask stores reason metadata when provided', function () {
    $user = User::factory()->create();
    Auth::login($user);

    $hub = createTaskHubForTaskService($user, 'Hub Kanban Reason', 'HUBR');

    $todo = TaskStatus::create([
        'title' => 'Todo',
        'color' => 'gray',
        'color_code_tailwind' => 'bg-gray-100 text-gray-800',
        'is_default' => true,
        'is_active' => true,
    ]);

    $done = TaskStatus::create([
        'title' => 'Done',
        'color' => 'green',
        'color_code_tailwind' => 'bg-green-100 text-green-700',
        'is_default' => false,
        'is_active' => false,
    ]);

    $task = Task::create([
        'task_hub_id' => $hub->id,
        'title' => 'Task Reason',
        'task_status_id' => $todo->id,
        'kanban_order' => 1,
    ]);

    app(TaskService::class)->moveKanbanTask(
        $hub->uuid,
        $task->id,
        $todo->id,
        $done->id,
        [],
        [$task->id],
        'Finalizado com evidÃªncias',
        'completion'
    );

    $activity = TaskActivity::where('task_id', $task->id)
        ->where('type', 'kanban_move')
        ->first();

    expect($activity)->not->toBeNull();
    expect($activity->meta['reason'])->toBe('Finalizado com evidÃªncias');
    expect($activity->meta['reason_type'])->toBe('completion');
});

test('moveKanbanTask sets finished_at for terminal status and clears on reopen', function () {
    $user = User::factory()->create();
    Auth::login($user);

    $hub = createTaskHubForTaskService($user, 'Hub Kanban Finish', 'HUBF');

    $inProgress = TaskStatus::create([
        'title' => 'Em andamento',
        'color' => 'blue',
        'color_code_tailwind' => 'bg-blue-100 text-blue-700',
        'is_default' => false,
        'is_active' => true,
    ]);

    $done = TaskStatus::create([
        'title' => 'Concluído',
        'color' => 'green',
        'color_code_tailwind' => 'bg-green-100 text-green-700',
        'is_default' => false,
        'is_active' => false,
    ]);

    $cancelled = TaskStatus::create([
        'title' => 'Cancelado',
        'color' => 'red',
        'color_code_tailwind' => 'bg-red-100 text-red-700',
        'is_default' => false,
        'is_active' => false,
    ]);

    $task = Task::create([
        'task_hub_id' => $hub->id,
        'title' => 'Task Finish',
        'task_status_id' => $inProgress->id,
        'kanban_order' => 1,
    ]);

    app(TaskService::class)->moveKanbanTask(
        $hub->uuid,
        $task->id,
        $inProgress->id,
        $done->id,
        [],
        [$task->id],
        'Finalizado',
        'completion'
    );

    $task->refresh();
    expect($task->finished_at)->not->toBeNull();

    app(TaskService::class)->moveKanbanTask(
        $hub->uuid,
        $task->id,
        $done->id,
        $inProgress->id,
        [$task->id],
        [$task->id],
        'Reaberto',
        'reopen'
    );

    $task->refresh();
    expect($task->finished_at)->toBeNull();

    app(TaskService::class)->moveKanbanTask(
        $hub->uuid,
        $task->id,
        $inProgress->id,
        $cancelled->id,
        [$task->id],
        [$task->id],
        'Cancelado',
        'cancellation'
    );

    $task->refresh();
    expect($task->finished_at)->not->toBeNull();
});

test('moveKanbanStep updates status, ordering, and history', function () {
    $user = User::factory()->create();
    Auth::login($user);

    $hub = createTaskHubForTaskService($user, 'Hub Steps', 'HUBS');

    $pending = TaskStepStatus::create(['title' => 'Pendente']);
    $running = TaskStepStatus::create(['title' => 'Em execucao']);

    $task = Task::create([
        'task_hub_id' => $hub->id,
        'title' => 'Task Steps',
    ]);

    $stepA1 = TaskStep::create([
        'task_id' => $task->id,
        'task_hub_id' => $hub->id,
        'title' => 'Step A1',
        'task_status_id' => $pending->id,
        'kanban_order' => 1,
    ]);

    $stepA2 = TaskStep::create([
        'task_id' => $task->id,
        'task_hub_id' => $hub->id,
        'title' => 'Step A2',
        'task_status_id' => $pending->id,
        'kanban_order' => 2,
    ]);

    $stepB1 = TaskStep::create([
        'task_id' => $task->id,
        'task_hub_id' => $hub->id,
        'title' => 'Step B1',
        'task_status_id' => $running->id,
        'kanban_order' => 1,
    ]);

    app(TaskService::class)->moveKanbanStep(
        $hub->uuid,
        $stepA2->id,
        $pending->id,
        $running->id,
        [$stepA1->id],
        [$stepB1->id, $stepA2->id],
        null,
        null
    );

    $stepA2->refresh();

    expect($stepA2->task_status_id)->toBe($running->id);
    expect(
        TaskStep::query()
            ->where('task_status_id', $pending->id)
            ->orderBy('kanban_order')
            ->pluck('id')
            ->all()
    )->toBe([$stepA1->id]);

    expect(
        TaskStep::query()
            ->where('task_status_id', $running->id)
            ->orderBy('kanban_order')
            ->pluck('id')
            ->all()
    )->toBe([$stepB1->id, $stepA2->id]);

    expect(
        TaskStepActivity::where('task_step_id', $stepA2->id)
            ->where('type', 'kanban_move')
            ->count()
    )->toBe(1);
});

test('moveKanbanStep stores completion comment and completion log in task activities', function () {
    $user = User::factory()->create();
    Auth::login($user);

    $hub = createTaskHubForTaskService($user, 'Hub Step Completion', 'HUBC');

    $pending = TaskStepStatus::create(['title' => 'Pendente']);
    $done = TaskStepStatus::create(['title' => 'Concluída']);

    $task = Task::create([
        'task_hub_id' => $hub->id,
        'title' => 'Task Step Completion',
    ]);

    $step = TaskStep::create([
        'task_id' => $task->id,
        'task_hub_id' => $hub->id,
        'title' => 'Etapa Final',
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
        'Entrega validada pelo setor.',
        'completion'
    );

    $taskComments = TaskActivity::where('task_id', $task->id)
        ->orderBy('id')
        ->get();

    expect($taskComments)->toHaveCount(2);
    expect($taskComments[0]->type)->toBe('comment');
    expect($taskComments[0]->description)->toBe('Entrega validada pelo setor.');
    expect($taskComments[1]->type)->toBe('step_finished_change');
    expect($taskComments[1]->description)->toContain('concluiu a etapa Etapa Final');
});

test('completeStep requires comment flow to write task activities', function () {
    $user = User::factory()->create();
    Auth::login($user);

    $hub = createTaskHubForTaskService($user, 'Hub Complete Step', 'HUBF');

    $done = TaskStepStatus::create(['title' => 'Concluída']);

    $task = Task::create([
        'task_hub_id' => $hub->id,
        'title' => 'Task Complete Step',
    ]);

    $step = TaskStep::create([
        'task_id' => $task->id,
        'task_hub_id' => $hub->id,
        'title' => 'Etapa Completa',
    ]);

    app(TaskService::class)->completeStep($step->id, 'Concluída após revisão final.');

    $step->refresh();

    expect($step->task_status_id)->toBe($done->id);
    expect($step->finished_at)->not->toBeNull();
    expect(TaskActivity::where('task_id', $task->id)->where('type', 'comment')->count())->toBe(1);
    expect(TaskActivity::where('task_id', $task->id)->where('type', 'step_finished_change')->count())->toBe(1);
});

test('moveKanbanStep stores reopen reason and reopen log in task activities', function () {
    $user = User::factory()->create();
    Auth::login($user);

    $hub = createTaskHubForTaskService($user, 'Hub Step Reopen', 'HUBR');

    $pending = TaskStepStatus::create(['title' => 'Pendente']);
    $done = TaskStepStatus::create(['title' => 'Concluída']);

    $task = Task::create([
        'task_hub_id' => $hub->id,
        'title' => 'Task Step Reopen',
    ]);

    $step = TaskStep::create([
        'task_id' => $task->id,
        'task_hub_id' => $hub->id,
        'title' => 'Etapa Reaberta',
        'task_status_id' => $done->id,
        'finished_at' => now(),
        'kanban_order' => 1,
    ]);

    app(TaskService::class)->moveKanbanStep(
        $hub->uuid,
        $step->id,
        $done->id,
        $pending->id,
        [$step->id],
        [$step->id],
        'Necessário complementar a evidência.',
        'reopen'
    );

    $taskActivities = TaskActivity::where('task_id', $task->id)
        ->orderBy('id')
        ->get();

    expect($taskActivities)->toHaveCount(2);
    expect($taskActivities[0]->type)->toBe('comment');
    expect($taskActivities[0]->description)->toBe('Necessário complementar a evidência.');
    expect($taskActivities[1]->type)->toBe('step_reopen_change');
    expect($taskActivities[1]->description)->toContain('reabriu a etapa Etapa Reaberta');
});

test('moveKanbanStep stores cancellation reason and cancellation log in task activities', function () {
    $user = User::factory()->create();
    Auth::login($user);

    $hub = createTaskHubForTaskService($user, 'Hub Step Cancel', 'HUBX');

    $pending = TaskStepStatus::create(['title' => 'Pendente']);
    $cancelled = TaskStepStatus::create(['title' => 'Cancelada']);

    $task = Task::create([
        'task_hub_id' => $hub->id,
        'title' => 'Task Step Cancel',
    ]);

    $step = TaskStep::create([
        'task_id' => $task->id,
        'task_hub_id' => $hub->id,
        'title' => 'Etapa Cancelada',
        'task_status_id' => $pending->id,
        'kanban_order' => 1,
    ]);

    app(TaskService::class)->moveKanbanStep(
        $hub->uuid,
        $step->id,
        $pending->id,
        $cancelled->id,
        [],
        [$step->id],
        'Cancelada por mudança de escopo.',
        'cancellation'
    );

    $taskActivities = TaskActivity::where('task_id', $task->id)
        ->orderBy('id')
        ->get();

    expect($taskActivities)->toHaveCount(2);
    expect($taskActivities[0]->type)->toBe('comment');
    expect($taskActivities[0]->description)->toBe('Cancelada por mudança de escopo.');
    expect($taskActivities[1]->type)->toBe('step_cancellation_change');
    expect($taskActivities[1]->description)->toContain('cancelou a etapa Etapa Cancelada');
});

test('moveKanbanStep does not allow switching directly between terminal step statuses', function () {
    $user = User::factory()->create();
    Auth::login($user);

    $hub = createTaskHubForTaskService($user, 'Hub Step Terminal Swap', 'HUBT');

    $done = TaskStepStatus::create(['title' => 'Concluída']);
    $cancelled = TaskStepStatus::create(['title' => 'Cancelada']);

    $task = Task::create([
        'task_hub_id' => $hub->id,
        'title' => 'Task Terminal Swap',
    ]);

    $step = TaskStep::create([
        'task_id' => $task->id,
        'task_hub_id' => $hub->id,
        'title' => 'Etapa Terminal',
        'task_status_id' => $done->id,
        'finished_at' => now(),
        'kanban_order' => 1,
    ]);

    app(TaskService::class)->moveKanbanStep(
        $hub->uuid,
        $step->id,
        $done->id,
        $cancelled->id,
        [$step->id],
        [$step->id],
        'Tentativa inválida',
        'cancellation'
    );

    $step->refresh();

    expect($step->task_status_id)->toBe($done->id);
    expect(TaskActivity::where('task_id', $task->id)->count())->toBe(0);
});

test('kanban includes tasks without status', function () {
    $user = User::factory()->create();
    Auth::login($user);

    $hub = createTaskHubForTaskService($user, 'Hub Kanban Status', 'HUBS');

    Task::create([
        'task_hub_id' => $hub->id,
        'title' => 'Sem status',
        'task_status_id' => null,
        'kanban_order' => 1,
    ]);

    $columns = app(TaskService::class)->kanban($hub->uuid);
    $semStatus = collect($columns)->firstWhere('status_id', 0);

    expect($semStatus)->not->toBeNull();
    expect($semStatus['tasks']->count())->toBe(1);
});

test('kanban hides tasks and steps finished more than three days ago', function () {
    $user = User::factory()->create();
    Auth::login($user);

    $hub = createTaskHubForTaskService($user, 'Hub Kanban Aging', 'HUBA');

    $inProgress = TaskStatus::create(['title' => 'Em andamento']);
    $done = TaskStatus::create(['title' => 'Concluído']);
    $stepDone = TaskStepStatus::create(['title' => 'Concluída']);

    $oldTask = Task::create([
        'task_hub_id' => $hub->id,
        'title' => 'Old Task',
        'task_status_id' => $done->id,
        'finished_at' => now()->subDays(4),
        'kanban_order' => 1,
    ]);

    $recentTask = Task::create([
        'task_hub_id' => $hub->id,
        'title' => 'Recent Task',
        'task_status_id' => $done->id,
        'finished_at' => now()->subDays(2),
        'kanban_order' => 2,
    ]);

    $taskForSteps = Task::create([
        'task_hub_id' => $hub->id,
        'title' => 'Task Steps',
        'task_status_id' => $inProgress->id,
        'kanban_order' => 3,
    ]);

    $oldStep = TaskStep::create([
        'task_id' => $taskForSteps->id,
        'task_hub_id' => $hub->id,
        'title' => 'Old Step',
        'task_status_id' => $stepDone->id,
        'finished_at' => now()->subDays(4),
        'kanban_order' => 1,
    ]);

    $recentStep = TaskStep::create([
        'task_id' => $taskForSteps->id,
        'task_hub_id' => $hub->id,
        'title' => 'Recent Step',
        'task_status_id' => $stepDone->id,
        'finished_at' => now()->subDays(2),
        'kanban_order' => 2,
    ]);

    $taskKanban = app(TaskService::class)->kanban($hub->uuid);
    $taskIds = collect($taskKanban)->flatMap(fn ($column) => $column['tasks']->pluck('id'))->all();

    expect($taskIds)->not->toContain($oldTask->id);
    expect($taskIds)->toContain($recentTask->id);

    $stepKanban = app(TaskService::class)->stepKanban($hub->uuid);
    $stepIds = collect($stepKanban)->flatMap(fn ($column) => $column['steps']->pluck('id'))->all();

    expect($stepIds)->not->toContain($oldStep->id);
    expect($stepIds)->toContain($recentStep->id);
});
