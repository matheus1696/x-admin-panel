<?php

use App\Enums\Process\ProcessEventType;
use App\Models\Process\ProcessStatus;
use App\Models\Administration\User\User;
use App\Models\Organization\OrganizationChart\OrganizationChart;
use App\Models\Organization\Workflow\Workflow;
use App\Models\Organization\Workflow\WorkflowStep;
use App\Models\Process\ProcessEvent;
use App\Models\Process\ProcessStep;
use App\Services\Process\ProcessService;

test('process service opens process and logs creation event', function () {
    $user = User::factory()->create();
    $service = app(ProcessService::class);

    $process = $service->open([
        'title' => 'Processo de Contratacao',
        'description' => 'Descricao teste',
        'priority' => 'high',
    ], $user->id);

    expect($process->title)->toBe('Processo de Contratacao')
        ->and($process->status)->toBe(ProcessStatus::IN_PROGRESS)
        ->and($process->started_at)->not->toBeNull()
        ->and($process->code)->toStartWith('PRC')
        ->and($process->events()->count())->toBe(2);
});

test('process service auto assigns first workflow step organization and starts process', function () {
    $user = User::factory()->create();
    $firstSectorUser = User::factory()->create();
    $secondSectorUser = User::factory()->create();
    $service = app(ProcessService::class);

    $firstOrganization = OrganizationChart::query()->create([
        'title' => 'Setor Inicial',
        'filter' => 'setor inicial',
        'hierarchy' => 0,
        'number_hierarchy' => 1,
        'order' => '001',
    ]);

    $secondOrganization = OrganizationChart::query()->create([
        'title' => 'Setor Final',
        'filter' => 'setor final',
        'hierarchy' => 0,
        'number_hierarchy' => 2,
        'order' => '002',
    ]);
    $firstSectorUser->organizations()->attach($firstOrganization->id);
    $secondSectorUser->organizations()->attach($secondOrganization->id);

    $workflow = Workflow::query()->create([
        'title' => 'Fluxo de Processamento',
        'filter' => 'fluxo de processamento',
        'description' => 'Fluxo para teste',
        'is_active' => true,
    ]);

    WorkflowStep::query()->create([
        'workflow_id' => $workflow->id,
        'title' => 'Primeira etapa',
        'filter' => 'primeira etapa',
        'step_order' => 1,
        'deadline_days' => 2,
        'required' => true,
        'allow_parallel' => false,
        'organization_id' => $firstOrganization->id,
    ]);

    WorkflowStep::query()->create([
        'workflow_id' => $workflow->id,
        'title' => 'Segunda etapa',
        'filter' => 'segunda etapa',
        'step_order' => 2,
        'deadline_days' => 3,
        'required' => true,
        'allow_parallel' => false,
        'organization_id' => $secondOrganization->id,
    ]);

    $process = $service->open([
        'title' => 'Processo com fluxo',
        'workflow_id' => $workflow->id,
    ], $user->id);

    expect($process->status)->toBe(ProcessStatus::IN_PROGRESS)
        ->and($process->started_at)->not->toBeNull()
        ->and($process->organization_id)->toBe($firstOrganization->id)
        ->and($process->organizations()->pluck('organization_charts.id')->all())->toBe([$firstOrganization->id, $secondOrganization->id])
        ->and($process->events()->count())->toBe(2)
        ->and($user->notifications()->count())->toBe(1)
        ->and($firstSectorUser->notifications()->count())->toBe(1)
        ->and($secondSectorUser->notifications()->count())->toBe(1);
});

test('process visibility uses owner and linked process sectors', function () {
    $owner = User::factory()->create();
    $sectorUser = User::factory()->create();
    $outsider = User::factory()->create();
    $service = app(ProcessService::class);

    $organization = OrganizationChart::query()->create([
        'title' => 'Setor de Visibilidade',
        'filter' => 'setor de visibilidade',
        'hierarchy' => 0,
        'number_hierarchy' => 9,
        'order' => '009',
    ]);

    $sectorUser->organizations()->attach($organization->id);

    $process = \App\Models\Process\Process::query()->create([
        'title' => 'Processo com setores vinculados',
        'description' => 'Descricao',
        'opened_by' => $owner->id,
        'owner_id' => $owner->id,
        'priority' => 'normal',
        'status' => ProcessStatus::IN_PROGRESS,
        'started_at' => now(),
    ]);

    ProcessStep::query()->create([
        'process_id' => $process->id,
        'step_order' => 1,
        'title' => 'Etapa setor',
        'organization_id' => $organization->id,
        'deadline_days' => 1,
        'required' => true,
        'is_current' => true,
        'status' => 'IN_PROGRESS',
        'started_at' => now(),
    ]);

    $process->organizations()->sync([$organization->id]);

    expect($service->userCanView($process->fresh('organizations'), $owner->id))->toBeTrue()
        ->and($service->userCanView($process->fresh('organizations'), $sectorUser->id))->toBeTrue()
        ->and($service->userCanView($process->fresh('organizations'), $outsider->id))->toBeFalse();
});

test('process service index orders by latest updates first', function () {
    $user = User::factory()->create();
    $service = app(ProcessService::class);

    $olderUpdated = \App\Models\Process\Process::query()->create([
        'title' => 'Processo atualizado antes',
        'description' => 'Descricao',
        'opened_by' => $user->id,
        'owner_id' => $user->id,
        'priority' => 'normal',
        'status' => ProcessStatus::IN_PROGRESS,
        'created_at' => now()->subDays(3),
        'updated_at' => now()->subDays(2),
    ]);

    $latestUpdated = \App\Models\Process\Process::query()->create([
        'title' => 'Processo atualizado por ultimo',
        'description' => 'Descricao',
        'opened_by' => $user->id,
        'owner_id' => $user->id,
        'priority' => 'normal',
        'status' => ProcessStatus::IN_PROGRESS,
        'created_at' => now()->subDays(5),
        'updated_at' => now()->subHour(),
    ]);

    \Illuminate\Support\Facades\DB::table('processes')
        ->where('id', $olderUpdated->id)
        ->update(['updated_at' => now()->subDays(2)]);

    \Illuminate\Support\Facades\DB::table('processes')
        ->where('id', $latestUpdated->id)
        ->update(['updated_at' => now()->subMinutes(5)]);

    $results = $service->index([
        'title' => '',
        'status' => 'all',
        'organization_id' => '',
        'perPage' => 10,
    ], $user->id);

    $ids = $results->getCollection()->pluck('id')->values()->all();

    expect($ids)->toContain($olderUpdated->id, $latestUpdated->id)
        ->and($ids[0])->toBe($latestUpdated->id);
});

test('process service resolves unseen updates by user last view timestamp', function () {
    $user = User::factory()->create();
    $service = app(ProcessService::class);

    $process = \App\Models\Process\Process::query()->create([
        'title' => 'Processo com rastreio de visualizacao',
        'description' => 'Descricao',
        'opened_by' => $user->id,
        'owner_id' => $user->id,
        'priority' => 'normal',
        'status' => ProcessStatus::IN_PROGRESS,
    ]);

    $collection = collect([$process->fresh()]);
    $unseenBeforeView = $service->processIdsWithUnseenUpdates($collection, $user->id);

    expect($unseenBeforeView->contains($process->id))->toBeTrue();

    $service->markAsViewed($process, $user->id);
    $unseenAfterView = $service->processIdsWithUnseenUpdates(collect([$process->fresh()]), $user->id);

    expect($unseenAfterView->contains($process->id))->toBeFalse();

    \Illuminate\Support\Facades\DB::table('processes')
        ->where('id', $process->id)
        ->update(['updated_at' => now()->addSecond()]);

    $unseenAfterUpdate = $service->processIdsWithUnseenUpdates(collect([$process->fresh()]), $user->id);
    expect($unseenAfterUpdate->contains($process->id))->toBeTrue();
});

test('process dashboard aggregates process progress indicators', function () {
    $service = app(ProcessService::class);

    $organizationA = OrganizationChart::query()->create([
        'title' => 'Setor Dashboard A',
        'filter' => 'setor dashboard a',
        'hierarchy' => 0,
        'number_hierarchy' => 40,
        'order' => '040',
    ]);

    $organizationB = OrganizationChart::query()->create([
        'title' => 'Setor Dashboard B',
        'filter' => 'setor dashboard b',
        'hierarchy' => 0,
        'number_hierarchy' => 41,
        'order' => '041',
    ]);

    $owner = User::factory()->create();

    $overdueProcess = \App\Models\Process\Process::query()->create([
        'title' => 'Processo atrasado',
        'description' => 'Descricao',
        'organization_id' => $organizationA->id,
        'opened_by' => $owner->id,
        'owner_id' => $owner->id,
        'priority' => 'normal',
        'status' => ProcessStatus::IN_PROGRESS,
        'started_at' => now()->subDays(5),
        'created_at' => now()->subDays(5),
        'updated_at' => now()->subDays(1),
    ]);

    \App\Models\Process\ProcessStep::query()->create([
        'process_id' => $overdueProcess->id,
        'step_order' => 1,
        'title' => 'Etapa atrasada',
        'organization_id' => $organizationA->id,
        'deadline_days' => 2,
        'required' => true,
        'is_current' => true,
        'status' => 'IN_PROGRESS',
        'started_at' => now()->subDays(4),
    ]);

    $healthyProcess = \App\Models\Process\Process::query()->create([
        'title' => 'Processo no prazo',
        'description' => 'Descricao',
        'organization_id' => $organizationB->id,
        'opened_by' => $owner->id,
        'owner_id' => $owner->id,
        'priority' => 'normal',
        'status' => ProcessStatus::IN_PROGRESS,
        'created_at' => now()->subDays(2),
        'updated_at' => now()->subDay(),
    ]);

    \App\Models\Process\ProcessStep::query()->create([
        'process_id' => $healthyProcess->id,
        'step_order' => 1,
        'title' => 'Etapa em dia',
        'organization_id' => $organizationB->id,
        'deadline_days' => 5,
        'required' => true,
        'is_current' => true,
        'status' => 'IN_PROGRESS',
        'started_at' => now()->subDay(),
    ]);

    \App\Models\Process\ProcessStep::query()->create([
        'process_id' => $healthyProcess->id,
        'step_order' => 2,
        'title' => 'Etapa concluida',
        'organization_id' => $organizationB->id,
        'deadline_days' => 2,
        'required' => true,
        'is_current' => false,
        'status' => 'COMPLETED',
        'started_at' => now()->subDays(6),
        'completed_at' => now()->subDays(4),
    ]);

    $dashboard = $service->dashboard([
        'window' => '90d',
        'organization_id' => 'all',
    ]);

    expect($dashboard['total'])->toBe(2)
        ->and($dashboard['in_progress_total'])->toBe(2)
        ->and($dashboard['deadline_summary']['overdue'])->toBe(1)
        ->and($dashboard['deadline_summary']['on_time'])->toBe(1)
        ->and(collect($dashboard['current_sectors'])->pluck('label')->all())->toContain('Setor Dashboard A', 'Setor Dashboard B')
        ->and(collect($dashboard['average_sector_times'])->pluck('label')->all())->toContain('Setor Dashboard B')
        ->and(collect($dashboard['overdue_processes'])->pluck('title')->all())->toContain('Processo atrasado')
        ->and(collect($dashboard['healthy_processes'])->pluck('title')->all())->toContain('Processo no prazo');
});

test('process service advances current step and starts next step', function () {
    $user = User::factory()->create();
    $nextSectorUser = User::factory()->create();
    $service = app(ProcessService::class);

    $firstOrganization = OrganizationChart::query()->create([
        'title' => 'Setor Um',
        'filter' => 'setor um',
        'hierarchy' => 0,
        'number_hierarchy' => 10,
        'order' => '010',
    ]);

    $secondOrganization = OrganizationChart::query()->create([
        'title' => 'Setor Dois',
        'filter' => 'setor dois',
        'hierarchy' => 0,
        'number_hierarchy' => 11,
        'order' => '011',
    ]);
    $user->organizations()->attach($firstOrganization->id);
    $nextSectorUser->organizations()->attach($secondOrganization->id);

    $workflow = Workflow::query()->create([
        'title' => 'Fluxo Avanco',
        'filter' => 'fluxo avanco',
        'description' => 'Fluxo para validar avanco',
        'is_active' => true,
    ]);

    WorkflowStep::query()->create([
        'workflow_id' => $workflow->id,
        'title' => 'Etapa 1',
        'filter' => 'etapa 1',
        'step_order' => 1,
        'deadline_days' => 2,
        'required' => true,
        'allow_parallel' => false,
        'organization_id' => $firstOrganization->id,
    ]);

    WorkflowStep::query()->create([
        'workflow_id' => $workflow->id,
        'title' => 'Etapa 2',
        'filter' => 'etapa 2',
        'step_order' => 2,
        'deadline_days' => 3,
        'required' => true,
        'allow_parallel' => false,
        'organization_id' => $secondOrganization->id,
    ]);

    $process = $service->open([
        'title' => 'Processo para avancar',
        'workflow_id' => $workflow->id,
    ], $user->id);

    $process = $service->advanceStep($process, $user->id, 'Encaminhado para setor seguinte');
    $process->load('steps');

    $firstStep = $process->steps->firstWhere('step_order', 1);
    $secondStep = $process->steps->firstWhere('step_order', 2);

    $event = $process->events()
        ->where('event_type', ProcessEventType::FORWARDED->value)
        ->latest('created_at')
        ->first();

    expect($firstStep?->status)->toBe('COMPLETED')
        ->and($firstStep?->is_current)->toBeFalse()
        ->and($secondStep?->status)->toBe('IN_PROGRESS')
        ->and($secondStep?->is_current)->toBeTrue()
        ->and($process->organization_id)->toBe($secondOrganization->id)
        ->and($event)->not->toBeNull()
        ->and($event->description)->toContain('Encaminhado para setor seguinte')
        ->and($nextSectorUser->notifications()->count())->toBe(2)
        ->and(
            $nextSectorUser->notifications()
                ->get()
                ->map(fn ($notification): string => (string) data_get($notification->data, 'title'))
                ->contains('Processo encaminhado')
        )->toBeTrue();
});

test('process service retreats current step and reopens previous step', function () {
    $user = User::factory()->create();
    $service = app(ProcessService::class);

    $firstOrganization = OrganizationChart::query()->create([
        'title' => 'Setor Tres',
        'filter' => 'setor tres',
        'hierarchy' => 0,
        'number_hierarchy' => 12,
        'order' => '012',
    ]);

    $secondOrganization = OrganizationChart::query()->create([
        'title' => 'Setor Quatro',
        'filter' => 'setor quatro',
        'hierarchy' => 0,
        'number_hierarchy' => 13,
        'order' => '013',
    ]);
    $user->organizations()->attach($firstOrganization->id);
    $user->organizations()->attach($secondOrganization->id);

    $workflow = Workflow::query()->create([
        'title' => 'Fluxo Retrocesso',
        'filter' => 'fluxo retrocesso',
        'description' => 'Fluxo para validar retrocesso',
        'is_active' => true,
    ]);

    WorkflowStep::query()->create([
        'workflow_id' => $workflow->id,
        'title' => 'Etapa A',
        'filter' => 'etapa a',
        'step_order' => 1,
        'deadline_days' => 2,
        'required' => true,
        'allow_parallel' => false,
        'organization_id' => $firstOrganization->id,
    ]);

    WorkflowStep::query()->create([
        'workflow_id' => $workflow->id,
        'title' => 'Etapa B',
        'filter' => 'etapa b',
        'step_order' => 2,
        'deadline_days' => 3,
        'required' => true,
        'allow_parallel' => false,
        'organization_id' => $secondOrganization->id,
    ]);

    $process = $service->open([
        'title' => 'Processo para retroceder',
        'workflow_id' => $workflow->id,
    ], $user->id);

    $process = $service->advanceStep($process, $user->id, 'Avanco para etapa B');
    $process = $service->retreatStep($process, $user->id, 'Retorno para etapa anterior');
    $process->load('steps');

    $firstStep = $process->steps->firstWhere('step_order', 1);
    $secondStep = $process->steps->firstWhere('step_order', 2);

    $event = $process->events()
        ->where('event_type', ProcessEventType::RETURNED->value)
        ->latest('created_at')
        ->first();

    expect($firstStep?->status)->toBe('IN_PROGRESS')
        ->and($firstStep?->is_current)->toBeTrue()
        ->and($secondStep?->status)->toBe('PENDING')
        ->and($secondStep?->is_current)->toBeFalse()
        ->and($process->organization_id)->toBe($firstOrganization->id)
        ->and($event)->not->toBeNull()
        ->and($event->description)->toContain('Retorno para etapa anterior');
});

test('process service records standalone comment as dispatch event', function () {
    $user = User::factory()->create();
    $service = app(ProcessService::class);

    $process = $service->open([
        'title' => 'Processo com comentario',
        'description' => 'Teste de comentario',
    ], $user->id);

    $process = $service->comment($process, $user->id, 'Comentario de despacho');

    $event = $process->events()
        ->where('event_type', ProcessEventType::COMMENTED->value)
        ->latest('created_at')
        ->first();

    expect($event)->not->toBeNull()
        ->and($event->description)->toContain('Comentario de despacho')
        ->and($user->notifications()->count())->toBe(2)
        ->and(
            $user->notifications()
                ->get()
                ->map(fn ($notification): string => (string) data_get($notification->data, 'title'))
                ->contains('Novo despacho no processo')
        )->toBeTrue();
});

test('process service assigns owner and logs standardized owner assignment event', function () {
    $actor = User::factory()->create();
    $newOwner = User::factory()->create();
    $service = app(ProcessService::class);

    $organization = OrganizationChart::query()->create([
        'title' => 'Setor Responsavel',
        'filter' => 'setor responsavel',
        'hierarchy' => 0,
        'number_hierarchy' => 20,
        'order' => '020',
    ]);

    $newOwner->organizations()->attach($organization->id);
    $actor->organizations()->attach($organization->id);

    $workflow = Workflow::query()->create([
        'title' => 'Fluxo Atribuicao',
        'filter' => 'fluxo atribuicao',
        'description' => 'Fluxo para atribuicao',
        'is_active' => true,
    ]);

    WorkflowStep::query()->create([
        'workflow_id' => $workflow->id,
        'title' => 'Etapa unica',
        'filter' => 'etapa unica',
        'step_order' => 1,
        'deadline_days' => 2,
        'required' => true,
        'allow_parallel' => false,
        'organization_id' => $organization->id,
    ]);

    $process = $service->open([
        'title' => 'Processo para atribuicao',
        'description' => 'Teste de atribuicao',
        'workflow_id' => $workflow->id,
    ], $actor->id);

    $notificationsBeforeAssign = $newOwner->notifications()->count();

    $process = $service->assignOwner($process, $actor->id, $newOwner->id);

    $event = $process->events()
        ->where('event_type', ProcessEventType::OWNER_ASSIGNED->value)
        ->latest('created_at')
        ->first();

    expect($process->owner_id)->toBe($newOwner->id)
        ->and($event)->not->toBeNull()
        ->and($event->description)->toContain('Etapa atribuida a')
        ->and($event->description)->toContain($newOwner->name)
        ->and($event->description)->toContain((string) $newOwner->id)
        ->and($newOwner->notifications()->count())->toBe($notificationsBeforeAssign + 1)
        ->and(
            $newOwner->notifications()
                ->get()
                ->map(fn ($notification): string => (string) data_get($notification->data, 'title'))
                ->contains('Responsavel atribuido no processo')
        )->toBeTrue();
});

test('process events use sequential event_number per process', function () {
    $user = User::factory()->create();
    $service = app(ProcessService::class);

    $process = $service->open([
        'title' => 'Processo com numeracao',
        'description' => 'Teste sequencial',
    ], $user->id);

    $service->comment($process, $user->id, 'Primeiro despacho');
    $service->comment($process, $user->id, 'Segundo despacho');

    $numbers = ProcessEvent::query()
        ->where('process_id', $process->id)
        ->orderBy('event_number')
        ->pluck('event_number')
        ->all();

    expect($numbers)->toBe([1, 2, 3, 4]);
});

test('process service concludes process when current step is the last one', function () {
    $user = User::factory()->create();
    $service = app(ProcessService::class);

    $organization = OrganizationChart::query()->create([
        'title' => 'Setor Final',
        'filter' => 'setor final',
        'hierarchy' => 0,
        'number_hierarchy' => 50,
        'order' => '050',
    ]);

    $user->organizations()->attach($organization->id);

    $workflow = Workflow::query()->create([
        'title' => 'Fluxo de conclusao',
        'filter' => 'fluxo de conclusao',
        'description' => 'Fluxo com etapa final',
        'is_active' => true,
    ]);

    WorkflowStep::query()->create([
        'workflow_id' => $workflow->id,
        'title' => 'Etapa final',
        'filter' => 'etapa final',
        'step_order' => 1,
        'deadline_days' => 2,
        'required' => true,
        'allow_parallel' => false,
        'organization_id' => $organization->id,
    ]);

    $process = $service->open([
        'title' => 'Processo para conclusao',
        'description' => 'Descricao',
        'workflow_id' => $workflow->id,
        'owner_id' => $user->id,
    ], $user->id);

    $process = $service->concludeProcess($process, $user->id, 'Conclusao na etapa final');
    $process->load('steps');

    $finalStep = $process->steps->firstWhere('step_order', 1);
    $event = $process->events()
        ->where('event_type', ProcessEventType::CLOSED->value)
        ->latest('created_at')
        ->first();

    expect($process->status)->toBe(ProcessStatus::CLOSED)
        ->and($process->closed_at)->not->toBeNull()
        ->and($finalStep?->status)->toBe('COMPLETED')
        ->and($finalStep?->is_current)->toBeFalse()
        ->and($finalStep?->completed_at)->not->toBeNull()
        ->and($event)->not->toBeNull();
});

test('process service blocks step transition when actor is outside current step organization', function () {
    $user = User::factory()->create();
    $service = app(ProcessService::class);

    $currentOrganization = OrganizationChart::query()->create([
        'title' => 'Setor Atual',
        'filter' => 'setor atual',
        'hierarchy' => 0,
        'number_hierarchy' => 30,
        'order' => '030',
    ]);

    $nextOrganization = OrganizationChart::query()->create([
        'title' => 'Setor Seguinte',
        'filter' => 'setor seguinte',
        'hierarchy' => 0,
        'number_hierarchy' => 31,
        'order' => '031',
    ]);

    $workflow = Workflow::query()->create([
        'title' => 'Fluxo Bloqueio',
        'filter' => 'fluxo bloqueio',
        'description' => 'Fluxo para validar bloqueio por setor',
        'is_active' => true,
    ]);

    WorkflowStep::query()->create([
        'workflow_id' => $workflow->id,
        'title' => 'Etapa atual',
        'filter' => 'etapa atual',
        'step_order' => 1,
        'deadline_days' => 2,
        'required' => true,
        'allow_parallel' => false,
        'organization_id' => $currentOrganization->id,
    ]);

    WorkflowStep::query()->create([
        'workflow_id' => $workflow->id,
        'title' => 'Etapa seguinte',
        'filter' => 'etapa seguinte',
        'step_order' => 2,
        'deadline_days' => 2,
        'required' => true,
        'allow_parallel' => false,
        'organization_id' => $nextOrganization->id,
    ]);

    $process = $service->open([
        'title' => 'Processo bloqueado por setor',
        'workflow_id' => $workflow->id,
    ], $user->id);

    expect(fn () => $service->advanceStep($process, $user->id, 'Tentativa sem vinculo'))
        ->toThrow(\InvalidArgumentException::class, 'Somente usuario do setor da etapa atual pode executar esta acao.');
});
