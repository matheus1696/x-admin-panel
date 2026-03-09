<?php

use App\Models\Administration\Task\TaskCategory;
use App\Models\Administration\Task\TaskPriority;
use App\Models\Administration\Task\TaskStatus;
use App\Models\Administration\Task\TaskStepStatus;
use App\Models\Administration\User\User;
use App\Models\Organization\OrganizationChart\OrganizationChart;
use App\Models\Organization\Workflow\Workflow;
use App\Models\Organization\Workflow\WorkflowStep;
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

if (! function_exists('createTaskStatusForHub')) {
    function createTaskStatusForHub(array $data): TaskStatus
    {
        $data['task_hub_id'] = $data['task_hub_id'] ?? TaskHub::query()->latest('id')->value('id');

        if (! $data['task_hub_id']) {
            throw new RuntimeException('Nenhum ambiente disponÃ­vel para vincular status de tarefa no teste.');
        }

        return TaskStatus::create($data);
    }
}

if (! function_exists('createTaskStepStatusForHub')) {
    function createTaskStepStatusForHub(array $data): TaskStepStatus
    {
        $data['task_hub_id'] = $data['task_hub_id'] ?? TaskHub::query()->latest('id')->value('id');

        if (! $data['task_hub_id']) {
            throw new RuntimeException('Nenhum ambiente disponÃ­vel para vincular status de etapa no teste.');
        }

        return TaskStepStatus::create($data);
    }
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

test('index applies list filters for task page', function () {
    $owner = User::factory()->create();
    $responsible = User::factory()->create();
    $otherResponsible = User::factory()->create();
    $hub = createTaskHubForTaskService($owner, 'Hub Filtros', 'HUBF');

    $organization = OrganizationChart::create([
        'acronym' => 'OPE',
        'title' => 'OperaÃƒÂ§ÃƒÂµes',
        'order' => 1,
        'hierarchy' => '1',
        'number_hierarchy' => 1,
    ]);
    $otherOrganization = OrganizationChart::create([
        'acronym' => 'COM',
        'title' => 'Comercial',
        'order' => 2,
        'hierarchy' => '2',
        'number_hierarchy' => 2,
    ]);

    $category = TaskCategory::create([
        'task_hub_id' => $hub->id,
        'title' => 'Operacional',
        'is_active' => true,
    ]);
    $otherCategory = TaskCategory::create([
        'task_hub_id' => $hub->id,
        'title' => 'EstratÃƒÂ©gica',
        'is_active' => true,
    ]);

    $priority = TaskPriority::create([
        'title' => 'Alta',
        'level' => 1,
        'is_active' => true,
    ]);
    $otherPriority = TaskPriority::create([
        'title' => 'Baixa',
        'level' => 2,
        'is_active' => true,
    ]);

    $status = createTaskStatusForHub(['title' => 'Em andamento']);
    $otherStatus = createTaskStatusForHub(['title' => 'Pendente']);

    $matchingTask = Task::create([
        'task_hub_id' => $hub->id,
        'title' => 'Task filtrada',
        'user_id' => $responsible->id,
        'task_category_id' => $category->id,
        'task_priority_id' => $priority->id,
        'task_status_id' => $status->id,
        'deadline_at' => now()->subDay(),
    ]);

    TaskStep::create([
        'task_id' => $matchingTask->id,
        'task_hub_id' => $hub->id,
        'title' => 'Etapa filtrada',
        'organization_id' => $organization->id,
    ]);

    $otherTask = Task::create([
        'task_hub_id' => $hub->id,
        'title' => 'Task fora do filtro',
        'user_id' => $otherResponsible->id,
        'task_category_id' => $otherCategory->id,
        'task_priority_id' => $otherPriority->id,
        'task_status_id' => $otherStatus->id,
        'deadline_at' => now()->addDay(),
    ]);

    TaskStep::create([
        'task_id' => $otherTask->id,
        'task_hub_id' => $hub->id,
        'title' => 'Outra etapa',
        'organization_id' => $otherOrganization->id,
    ]);

    $results = app(TaskService::class)->index($hub->uuid, [
        'title' => '',
        'organization_id' => (string) $organization->id,
        'user_id' => (string) $responsible->id,
        'task_category_id' => (string) $category->id,
        'task_status_id' => (string) $status->id,
        'task_priority_id' => (string) $priority->id,
        'is_overdue' => 'yes',
        'perPage' => 50,
    ]);

    expect($results->total())->toBe(1);
    expect($results->items()[0]->id)->toBe($matchingTask->id);
});

test('copyWorkflowToTask copies workflow steps with accumulated deadlines and blocks duplicates', function () {
    $owner = User::factory()->create();
    Auth::login($owner);

    $hub = createTaskHubForTaskService($owner, 'Hub Workflow Copy', 'HUWC');

    $defaultStepStatus = createTaskStepStatusForHub([
        'title' => 'Pendente',
        'is_default' => true,
    ]);

    $organizationA = OrganizationChart::create([
        'acronym' => 'TRI',
        'title' => 'Triagem',
        'order' => 1,
        'hierarchy' => '1',
        'number_hierarchy' => 1,
    ]);

    $organizationB = OrganizationChart::create([
        'acronym' => 'ANA',
        'title' => 'AnÃƒÂ¡lise',
        'order' => 2,
        'hierarchy' => '2',
        'number_hierarchy' => 2,
    ]);

    $workflow = Workflow::create([
        'title' => 'Fluxo Operacional',
        'is_active' => true,
        'total_estimated_days' => 5,
    ]);

    WorkflowStep::create([
        'workflow_id' => $workflow->id,
        'title' => 'Triar solicitaÃƒÂ§ÃƒÂ£o',
        'step_order' => 1,
        'deadline_days' => 2,
        'required' => true,
        'organization_id' => $organizationA->id,
    ]);

    WorkflowStep::create([
        'workflow_id' => $workflow->id,
        'title' => 'Analisar demanda',
        'step_order' => 2,
        'deadline_days' => 3,
        'required' => false,
        'allow_parallel' => true,
        'organization_id' => $organizationB->id,
    ]);

    $task = Task::create([
        'task_hub_id' => $hub->id,
        'title' => 'Task sem etapas',
    ])->fresh();

    $copied = app(TaskService::class)->copyWorkflowToTask($task->id, $workflow->id);

    $task->refresh();
    $steps = TaskStep::query()
        ->where('task_id', $task->id)
        ->orderBy('id')
        ->get();
    $baseDate = $task->created_at->copy()->startOfDay();

    expect($copied)->toBeTrue();
    expect($steps)->toHaveCount(2);
    expect($steps[0]->title)->toBe('Triar solicitaÃƒÂ§ÃƒÂ£o');
    expect($steps[0]->organization_id)->toBe($organizationA->id);
    expect($steps[0]->task_status_id)->toBe($defaultStepStatus->id);
    expect($steps[0]->workflow_step_order)->toBe(1);
    expect($steps[0]->is_required)->toBeTrue();
    expect($steps[0]->allow_parallel)->toBeFalse();
    expect($steps[0]->deadline_at?->format('Y-m-d'))->toBe($baseDate->copy()->addDays(2)->format('Y-m-d'));
    expect($steps[1]->title)->toBe('Analisar demanda');
    expect($steps[1]->organization_id)->toBe($organizationB->id);
    expect($steps[1]->workflow_step_order)->toBe(2);
    expect($steps[1]->is_required)->toBeFalse();
    expect($steps[1]->allow_parallel)->toBeTrue();
    expect($steps[1]->deadline_at?->format('Y-m-d'))->toBe($baseDate->copy()->addDays(5)->format('Y-m-d'));
    expect($task->deadline_at?->format('Y-m-d'))->toBe($baseDate->copy()->addDays(5)->format('Y-m-d'));
    expect(TaskActivity::where('task_id', $task->id)->where('type', 'workflow_copy')->count())->toBe(1);

    $copiedAgain = app(TaskService::class)->copyWorkflowToTask($task->id, $workflow->id);

    expect($copiedAgain)->toBeFalse();
    expect(TaskStep::query()->where('task_id', $task->id)->count())->toBe(2);
});

test('moveKanbanStep blocks starting next workflow step while previous required step is open', function () {
    $user = User::factory()->create();
    Auth::login($user);

    $hub = createTaskHubForTaskService($user, 'Hub Workflow Sequence', 'HUWS');

    $pending = createTaskStepStatusForHub(['title' => 'Pendente']);
    $running = createTaskStepStatusForHub(['title' => 'Em andamento']);

    $task = Task::create([
        'task_hub_id' => $hub->id,
        'title' => 'Task com fluxo',
    ]);

    $stepOne = TaskStep::create([
        'task_id' => $task->id,
        'task_hub_id' => $hub->id,
        'title' => 'Etapa 1',
        'task_status_id' => $pending->id,
        'workflow_step_order' => 1,
        'is_required' => true,
        'allow_parallel' => false,
        'kanban_order' => 1,
    ]);

    $stepTwo = TaskStep::create([
        'task_id' => $task->id,
        'task_hub_id' => $hub->id,
        'title' => 'Etapa 2',
        'task_status_id' => $pending->id,
        'workflow_step_order' => 2,
        'is_required' => true,
        'allow_parallel' => false,
        'kanban_order' => 2,
    ]);

    $moved = app(TaskService::class)->moveKanbanStep(
        $hub->uuid,
        $stepTwo->id,
        $pending->id,
        $running->id,
        [$stepOne->id],
        [$stepTwo->id],
        null,
        null
    );

    $stepTwo->refresh();

    expect($moved)->toBeFalse();
    expect($stepTwo->task_status_id)->toBe($pending->id);
    expect($stepTwo->started_at)->toBeNull();

    $stepOne->update([
        'task_status_id' => $running->id,
        'started_at' => now()->subDay(),
        'finished_at' => now(),
    ]);

    $movedAfterPreviousFinished = app(TaskService::class)->moveKanbanStep(
        $hub->uuid,
        $stepTwo->id,
        $pending->id,
        $running->id,
        [],
        [$stepTwo->id],
        null,
        null
    );

    $stepTwo->refresh();

    expect($movedAfterPreviousFinished)->toBeTrue();
    expect($stepTwo->task_status_id)->toBe($running->id);
    expect($stepTwo->started_at)->not->toBeNull();
});

test('moveKanbanStep allows starting next workflow step when previous required step is terminal even without finished_at', function () {
    $user = User::factory()->create();
    Auth::login($user);

    $hub = createTaskHubForTaskService($user, 'Hub Workflow Terminal Legacy', 'HWTL');

    $pending = createTaskStepStatusForHub(['title' => 'Pendente']);
    $running = createTaskStepStatusForHub(['title' => 'Em andamento']);
    $done = createTaskStepStatusForHub(['title' => 'ConcluÃ­da']);

    $task = Task::create([
        'task_hub_id' => $hub->id,
        'title' => 'Task com fluxo legado',
    ]);

    $stepOne = TaskStep::create([
        'task_id' => $task->id,
        'task_hub_id' => $hub->id,
        'title' => 'Etapa 1',
        'task_status_id' => $done->id,
        'workflow_step_order' => 1,
        'is_required' => true,
        'allow_parallel' => false,
        'kanban_order' => 1,
        'finished_at' => null,
    ]);

    $stepTwo = TaskStep::create([
        'task_id' => $task->id,
        'task_hub_id' => $hub->id,
        'title' => 'Etapa 2',
        'task_status_id' => $pending->id,
        'workflow_step_order' => 2,
        'is_required' => true,
        'allow_parallel' => false,
        'kanban_order' => 2,
    ]);

    $moved = app(TaskService::class)->moveKanbanStep(
        $hub->uuid,
        $stepTwo->id,
        $pending->id,
        $running->id,
        [$stepOne->id],
        [$stepTwo->id],
        null,
        null
    );

    $stepTwo->refresh();

    expect($moved)->toBeTrue();
    expect($stepTwo->task_status_id)->toBe($running->id);
    expect($stepTwo->started_at)->not->toBeNull();
});

test('dashboard aggregates task and step metrics for a hub', function () {
    $owner = User::factory()->create();
    $responsible = User::factory()->create();
    $hub = createTaskHubForTaskService($owner, 'Hub Dashboard', 'HUBD');

    $inProgressStatus = createTaskStatusForHub(['title' => 'Em andamento']);
    $completedStatus = createTaskStatusForHub(['title' => 'ConcluÃƒÂ­do']);
    $cancelledStatus = createTaskStatusForHub(['title' => 'Cancelado']);

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

    $pending = createTaskStepStatusForHub(['title' => 'Pendente']);
    $running = createTaskStepStatusForHub(['title' => 'Em execucao']);
    createTaskStepStatusForHub(['title' => 'ConcluÃƒÂ­da']);
    createTaskStepStatusForHub(['title' => 'Cancelada']);

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

    $ownedHub = createTaskHubForTaskService($viewer, 'Hub PrÃƒÂ³prio', 'HUBP');
    $sharedHub = createTaskHubForTaskService($owner, 'Hub Compartilhado', 'HUBC');
    $hiddenHub = createTaskHubForTaskService($owner, 'Hub Oculto', 'HUBO');

    TaskHubMember::create([
        'task_hub_id' => $sharedHub->id,
        'user_id' => $viewer->id,
    ]);

    $inProgressStatus = createTaskStatusForHub(['title' => 'Em andamento']);
    $completedStatus = createTaskStatusForHub(['title' => 'ConcluÃƒÂ­do']);
    $cancelledStatus = createTaskStatusForHub(['title' => 'Cancelado']);

    $ownedTask = Task::create([
        'task_hub_id' => $ownedHub->id,
        'title' => 'Tarefa PrÃƒÂ³pria',
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
        'title' => 'Tarefa ConcluÃƒÂ­da',
        'user_id' => $responsible->id,
        'task_status_id' => $completedStatus->id,
    ]);

    $hiddenTask = Task::create([
        'task_hub_id' => $hiddenHub->id,
        'title' => 'Tarefa Oculta',
        'task_status_id' => $cancelledStatus->id,
    ]);

    $pendingStepStatus = createTaskStepStatusForHub(['title' => 'Pendente']);
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
        'title' => 'Etapa VisÃƒÂ­vel',
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
    expect($overview)->not->toHaveKeys(['users', 'organizations', 'overdue_tasks']);
});

test('moveKanbanTask updates status, ordering, and history', function () {
    $user = User::factory()->create();
    Auth::login($user);

    $hub = createTaskHubForTaskService($user, 'Hub Kanban', 'HUBK');

    $todo = createTaskStatusForHub([
        'title' => 'Todo',
        'color' => 'gray',
        'color_code_tailwind' => 'bg-gray-100 text-gray-800',
        'is_default' => true,
        'is_active' => true,
    ]);

    $doing = createTaskStatusForHub([
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

    $todo = createTaskStatusForHub([
        'title' => 'Todo',
        'color' => 'gray',
        'color_code_tailwind' => 'bg-gray-100 text-gray-800',
        'is_default' => true,
        'is_active' => true,
    ]);

    $done = createTaskStatusForHub([
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
        'Finalizado com evidÃƒÂªncias',
        'completion'
    );

    $activity = TaskActivity::where('task_id', $task->id)
        ->where('type', 'kanban_move')
        ->first();

    expect($activity)->not->toBeNull();
    expect($activity->meta['reason'])->toBe('Finalizado com evidÃƒÂªncias');
    expect($activity->meta['reason_type'])->toBe('completion');
});

test('moveKanbanTask sets finished_at for terminal status and clears on reopen', function () {
    $user = User::factory()->create();
    Auth::login($user);

    $hub = createTaskHubForTaskService($user, 'Hub Kanban Finish', 'HUBF');

    $inProgress = createTaskStatusForHub([
        'title' => 'Em andamento',
        'color' => 'blue',
        'color_code_tailwind' => 'bg-blue-100 text-blue-700',
        'is_default' => false,
        'is_active' => true,
    ]);

    $done = createTaskStatusForHub([
        'title' => 'ConcluÃƒÂ­do',
        'color' => 'green',
        'color_code_tailwind' => 'bg-green-100 text-green-700',
        'is_default' => false,
        'is_active' => false,
    ]);

    $cancelled = createTaskStatusForHub([
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

test('moveKanbanTask does not conclude task when there are steps not concluded', function () {
    $user = User::factory()->create();
    Auth::login($user);

    $hub = createTaskHubForTaskService($user, 'Hub Task Completion Rule', 'HCTR');

    $inProgress = createTaskStatusForHub(['title' => 'Em andamento']);
    $done = createTaskStatusForHub(['title' => 'Concluído']);

    $stepPending = createTaskStepStatusForHub(['title' => 'Pendente']);
    createTaskStepStatusForHub(['title' => 'Concluída']);

    $task = Task::create([
        'task_hub_id' => $hub->id,
        'title' => 'Task bloqueada para conclusão',
        'task_status_id' => $inProgress->id,
        'kanban_order' => 1,
    ]);

    TaskStep::create([
        'task_id' => $task->id,
        'task_hub_id' => $hub->id,
        'title' => 'Etapa pendente',
        'task_status_id' => $stepPending->id,
        'kanban_order' => 1,
    ]);

    $moved = app(TaskService::class)->moveKanbanTask(
        $hub->uuid,
        $task->id,
        $inProgress->id,
        $done->id,
        [$task->id],
        [$task->id],
        'Tentativa de conclusão',
        'completion'
    );

    $task->refresh();

    expect($moved)->toBeFalse();
    expect($task->task_status_id)->toBe($inProgress->id);
    expect($task->finished_at)->toBeNull();
});

test('moveKanbanStep updates status, ordering, and history', function () {
    $user = User::factory()->create();
    Auth::login($user);

    $hub = createTaskHubForTaskService($user, 'Hub Steps', 'HUBS');

    $pending = createTaskStepStatusForHub(['title' => 'Pendente']);
    $running = createTaskStepStatusForHub(['title' => 'Em execucao']);

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
    expect($stepA2->started_at)->not->toBeNull();
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
    expect(
        TaskStepActivity::where('task_step_id', $stepA2->id)
            ->where('type', 'kanban_move')
            ->value('description')
    )->toContain('moveu a etapa no kanban para Em execucao');
});

test('moveKanbanStep stores completion comment and completion log in task activities', function () {
    $user = User::factory()->create();
    Auth::login($user);

    $hub = createTaskHubForTaskService($user, 'Hub Step Completion', 'HUBC');

    $pending = createTaskStepStatusForHub(['title' => 'Pendente']);
    $done = createTaskStepStatusForHub(['title' => 'ConcluÃƒÂ­da']);

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

test('changeStepStatus fills started_at and finished_at for steps', function () {
    $user = User::factory()->create();
    Auth::login($user);

    $hub = createTaskHubForTaskService($user, 'Hub Step Status Change', 'HUSC');

    $pending = createTaskStepStatusForHub(['title' => 'Pendente']);
    $inProgress = createTaskStepStatusForHub(['title' => 'Em andamento']);
    $done = createTaskStepStatusForHub(['title' => 'ConcluÃƒÂ­da']);

    $task = Task::create([
        'task_hub_id' => $hub->id,
        'title' => 'Task Step Status Change',
    ]);

    $step = TaskStep::create([
        'task_id' => $task->id,
        'task_hub_id' => $hub->id,
        'title' => 'Etapa com mudanÃƒÂ§a direta',
        'task_status_id' => $pending->id,
    ]);

    $step = app(TaskService::class)->changeStepStatus($step->id, $inProgress->id);

    expect($step->task_status_id)->toBe($inProgress->id);
    expect($step->started_at)->not->toBeNull();
    expect($step->finished_at)->toBeNull();

    $step = app(TaskService::class)->changeStepStatus($step->id, $done->id);

    expect($step->task_status_id)->toBe($done->id);
    expect($step->started_at)->not->toBeNull();
    expect($step->finished_at)->not->toBeNull();
});

test('completeStep requires comment flow to write task activities', function () {
    $user = User::factory()->create();
    Auth::login($user);

    $hub = createTaskHubForTaskService($user, 'Hub Complete Step', 'HUBF');

    $done = createTaskStepStatusForHub(['title' => 'ConcluÃƒÂ­da']);

    $task = Task::create([
        'task_hub_id' => $hub->id,
        'title' => 'Task Complete Step',
    ]);

    $step = TaskStep::create([
        'task_id' => $task->id,
        'task_hub_id' => $hub->id,
        'title' => 'Etapa Completa',
    ]);

    app(TaskService::class)->completeStep($step->id, 'ConcluÃƒÂ­da apÃƒÂ³s revisÃƒÂ£o final.');

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

    $pending = createTaskStepStatusForHub(['title' => 'Pendente']);
    $done = createTaskStepStatusForHub(['title' => 'ConcluÃƒÂ­da']);

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
        'NecessÃƒÂ¡rio complementar a evidÃƒÂªncia.',
        'reopen'
    );

    $taskActivities = TaskActivity::where('task_id', $task->id)
        ->orderBy('id')
        ->get();

    expect($taskActivities)->toHaveCount(2);
    expect($taskActivities[0]->type)->toBe('comment');
    expect($taskActivities[0]->description)->toBe('NecessÃƒÂ¡rio complementar a evidÃƒÂªncia.');
    expect($taskActivities[1]->type)->toBe('step_reopen_change');
    expect($taskActivities[1]->description)->toContain('reabriu a etapa Etapa Reaberta');
});

test('moveKanbanStep stores cancellation reason and cancellation log in task activities', function () {
    $user = User::factory()->create();
    Auth::login($user);

    $hub = createTaskHubForTaskService($user, 'Hub Step Cancel', 'HUBX');

    $pending = createTaskStepStatusForHub(['title' => 'Pendente']);
    $cancelled = createTaskStepStatusForHub(['title' => 'Cancelada']);

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
        'Cancelada por mudanÃƒÂ§a de escopo.',
        'cancellation'
    );

    $taskActivities = TaskActivity::where('task_id', $task->id)
        ->orderBy('id')
        ->get();

    expect($taskActivities)->toHaveCount(2);
    expect($taskActivities[0]->type)->toBe('comment');
    expect($taskActivities[0]->description)->toBe('Cancelada por mudanÃƒÂ§a de escopo.');
    expect($taskActivities[1]->type)->toBe('step_cancellation_change');
    expect($taskActivities[1]->description)->toContain('cancelou a etapa Etapa Cancelada');
});

test('moveKanbanStep does not allow switching directly between terminal step statuses', function () {
    $user = User::factory()->create();
    Auth::login($user);

    $hub = createTaskHubForTaskService($user, 'Hub Step Terminal Swap', 'HUBT');

    $done = createTaskStepStatusForHub(['title' => 'ConcluÃƒÂ­da']);
    $cancelled = createTaskStepStatusForHub(['title' => 'Cancelada']);

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
        'Tentativa invÃƒÂ¡lida',
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

    $inProgress = createTaskStatusForHub(['title' => 'Em andamento']);
    $done = createTaskStatusForHub(['title' => 'ConcluÃƒÂ­do']);
    $stepDone = createTaskStepStatusForHub(['title' => 'ConcluÃƒÂ­da']);

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

test('stepKanban filters steps by task, organization, and responsible user', function () {
    $owner = User::factory()->create();
    $responsibleA = User::factory()->create(['name' => 'Responsavel A']);
    $responsibleB = User::factory()->create(['name' => 'Responsavel B']);

    $hub = createTaskHubForTaskService($owner, 'Hub Filtros', 'HUBF');

    $status = createTaskStepStatusForHub([
        'title' => 'Em andamento',
    ]);

    $organizationA = OrganizationChart::create([
        'title' => 'Setor A',
        'acronym' => 'STA',
        'order' => 1,
        'hierarchy' => 0,
        'number_hierarchy' => '1',
    ]);

    $organizationB = OrganizationChart::create([
        'title' => 'Setor B',
        'acronym' => 'STB',
        'order' => 2,
        'hierarchy' => 0,
        'number_hierarchy' => '2',
    ]);

    $taskA = Task::create([
        'task_hub_id' => $hub->id,
        'title' => 'Projeto A',
    ]);

    $taskB = Task::create([
        'task_hub_id' => $hub->id,
        'title' => 'Projeto B',
    ]);

    $stepA = TaskStep::create([
        'task_id' => $taskA->id,
        'task_hub_id' => $hub->id,
        'title' => 'Etapa A',
        'task_status_id' => $status->id,
        'organization_id' => $organizationA->id,
        'user_id' => $responsibleA->id,
        'kanban_order' => 1,
    ]);

    $stepB = TaskStep::create([
        'task_id' => $taskB->id,
        'task_hub_id' => $hub->id,
        'title' => 'Etapa B',
        'task_status_id' => $status->id,
        'organization_id' => $organizationB->id,
        'user_id' => $responsibleB->id,
        'kanban_order' => 2,
    ]);

    $byTask = app(TaskService::class)->stepKanban($hub->uuid, [
        'task_id' => (string) $taskA->id,
    ]);

    $byOrganization = app(TaskService::class)->stepKanban($hub->uuid, [
        'organization_id' => (string) $organizationA->id,
    ]);

    $byUser = app(TaskService::class)->stepKanban($hub->uuid, [
        'user_id' => (string) $responsibleA->id,
    ]);

    expect(collect($byTask)->flatMap(fn ($column) => $column['steps']->pluck('id'))->all())->toBe([$stepA->id]);
    expect(collect($byOrganization)->flatMap(fn ($column) => $column['steps']->pluck('id'))->all())->toBe([$stepA->id]);
    expect(collect($byUser)->flatMap(fn ($column) => $column['steps']->pluck('id'))->all())->toBe([$stepA->id]);
    expect($stepB->id)->not->toBe($stepA->id);
});

test('addMember notifies the user when added to a task hub', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();

    $hub = createTaskHubForTaskService($owner, 'Hub Notificacao', 'HUBN');

    $added = app(TaskService::class)->addMember($hub->uuid, $owner->id, $member->id);

    expect($added)->toBeTrue();
    expect($member->notifications()->count())->toBe(1);
    expect($member->notifications()->first()->data['title'])->toBe('Voce foi associado a um ambiente de tarefas');
});

test('create notifies the responsible user when a task is assigned on creation', function () {
    $owner = User::factory()->create();
    $responsible = User::factory()->create();
    Auth::login($owner);

    $hub = createTaskHubForTaskService($owner, 'Hub Tarefa', 'HUBT');

    TaskHubMember::create([
        'task_hub_id' => $hub->id,
        'user_id' => $responsible->id,
    ]);

    app(TaskService::class)->create($hub->uuid, [
        'title' => 'Nova tarefa com responsavel',
        'user_id' => $responsible->id,
        'task_category_id' => null,
        'task_priority_id' => null,
        'task_status_id' => null,
        'description' => null,
        'deadline_at' => null,
    ]);

    expect($responsible->notifications()->count())->toBe(1);
    expect($responsible->notifications()->first()->data['title'])->toBe('Voce foi associado a uma tarefa');
});
