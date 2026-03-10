<?php

use App\Enums\Process\ProcessStatus;
use App\Enums\Process\ProcessEventType;
use App\Models\Administration\User\User;
use App\Models\Organization\OrganizationChart\OrganizationChart;
use App\Models\Organization\Workflow\Workflow;
use App\Models\Organization\Workflow\WorkflowStep;
use App\Models\Process\Process;
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
        ->and($process->status)->toBe(ProcessStatus::OPEN->value)
        ->and($process->code)->toStartWith('PRC')
        ->and($process->events()->count())->toBe(1);
});

test('process service auto assigns first workflow step organization and starts process', function () {
    $user = User::factory()->create();
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

    expect($process->status)->toBe(ProcessStatus::IN_PROGRESS->value)
        ->and($process->started_at)->not->toBeNull()
        ->and($process->organization_id)->toBe($firstOrganization->id)
        ->and($process->events()->count())->toBe(2);
});

test('process service starts and closes process with events', function () {
    $user = User::factory()->create();
    $service = app(ProcessService::class);

    $process = $service->open([
        'title' => 'Processo de Auditoria',
        'description' => null,
    ], $user->id);

    $process = $service->start($process, $user->id);
    $process = $service->close($process, $user->id, 'Processo concluido');

    expect($process->status)->toBe(ProcessStatus::CLOSED->value)
        ->and($process->closed_at)->not->toBeNull()
        ->and($process->events()->count())->toBe(3);
});

test('process service cancels process with required note', function () {
    $user = User::factory()->create();
    $service = app(ProcessService::class);

    $process = $service->open([
        'title' => 'Processo de Cancelamento',
    ], $user->id);

    $process = $service->cancel($process, $user->id, 'Solicitacao interrompida');

    expect($process->status)->toBe(ProcessStatus::CANCELLED->value)
        ->and($process->closed_at)->not->toBeNull()
        ->and($process->events()->count())->toBe(2);
});

test('process service rejects start when process is already in progress', function () {
    $user = User::factory()->create();
    $service = app(ProcessService::class);

    $process = Process::query()->create([
        'title' => 'Processo Ja Iniciado',
        'opened_by' => $user->id,
        'owner_id' => $user->id,
        'priority' => 'normal',
        'status' => ProcessStatus::IN_PROGRESS->value,
    ]);

    expect(fn () => $service->start($process, $user->id))
        ->toThrow(\InvalidArgumentException::class);
});

test('process service forwards to next step and logs dispatch event', function () {
    $user = User::factory()->create();
    $service = app(ProcessService::class);

    $firstOrganization = OrganizationChart::query()->create([
        'title' => 'Setor A',
        'filter' => 'setor a',
        'hierarchy' => 0,
        'number_hierarchy' => 1,
        'order' => '001',
    ]);

    $secondOrganization = OrganizationChart::query()->create([
        'title' => 'Setor B',
        'filter' => 'setor b',
        'hierarchy' => 0,
        'number_hierarchy' => 2,
        'order' => '002',
    ]);

    $workflow = Workflow::query()->create([
        'title' => 'Fluxo despacho',
        'filter' => 'fluxo despacho',
        'description' => null,
        'is_active' => true,
    ]);

    WorkflowStep::query()->create([
        'workflow_id' => $workflow->id,
        'title' => 'Etapa A',
        'filter' => 'etapa a',
        'step_order' => 1,
        'deadline_days' => 1,
        'required' => true,
        'allow_parallel' => false,
        'organization_id' => $firstOrganization->id,
    ]);

    WorkflowStep::query()->create([
        'workflow_id' => $workflow->id,
        'title' => 'Etapa B',
        'filter' => 'etapa b',
        'step_order' => 2,
        'deadline_days' => 2,
        'required' => true,
        'allow_parallel' => false,
        'organization_id' => $secondOrganization->id,
    ]);

    $process = $service->open([
        'title' => 'Processo despacho',
        'workflow_id' => $workflow->id,
    ], $user->id);

    $process = $service->forward($process, $user->id, 'Encaminhar para etapa B');

    $event = $process->events()
        ->where('event_type', ProcessEventType::FORWARDED->value)
        ->latest('created_at')
        ->first();

    expect($process->organization_id)->toBe($secondOrganization->id)
        ->and($event)->not->toBeNull()
        ->and($event->payload['comment'] ?? null)->toBe('Encaminhar para etapa B');
});

test('process service returns to previous step and logs dispatch event', function () {
    $user = User::factory()->create();
    $service = app(ProcessService::class);

    $firstOrganization = OrganizationChart::query()->create([
        'title' => 'Setor C',
        'filter' => 'setor c',
        'hierarchy' => 0,
        'number_hierarchy' => 3,
        'order' => '003',
    ]);

    $secondOrganization = OrganizationChart::query()->create([
        'title' => 'Setor D',
        'filter' => 'setor d',
        'hierarchy' => 0,
        'number_hierarchy' => 4,
        'order' => '004',
    ]);

    $workflow = Workflow::query()->create([
        'title' => 'Fluxo retorno',
        'filter' => 'fluxo retorno',
        'description' => null,
        'is_active' => true,
    ]);

    WorkflowStep::query()->create([
        'workflow_id' => $workflow->id,
        'title' => 'Etapa C',
        'filter' => 'etapa c',
        'step_order' => 1,
        'deadline_days' => 1,
        'required' => true,
        'allow_parallel' => false,
        'organization_id' => $firstOrganization->id,
    ]);

    WorkflowStep::query()->create([
        'workflow_id' => $workflow->id,
        'title' => 'Etapa D',
        'filter' => 'etapa d',
        'step_order' => 2,
        'deadline_days' => 2,
        'required' => true,
        'allow_parallel' => false,
        'organization_id' => $secondOrganization->id,
    ]);

    $process = $service->open([
        'title' => 'Processo retorno',
        'workflow_id' => $workflow->id,
    ], $user->id);

    $process = $service->forward($process, $user->id, 'Avanco inicial');
    $process = $service->backward($process, $user->id, 'Retornar para revisao');

    $event = $process->events()
        ->where('event_type', ProcessEventType::RETURNED->value)
        ->latest('created_at')
        ->first();

    expect($process->organization_id)->toBe($firstOrganization->id)
        ->and($event)->not->toBeNull()
        ->and($event->payload['comment'] ?? null)->toBe('Retornar para revisao');
});
