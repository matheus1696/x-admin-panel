<?php

use App\Enums\Process\ProcessStatus;
use App\Models\Administration\User\User;
use App\Models\Organization\OrganizationChart\OrganizationChart;
use App\Models\Organization\Workflow\Workflow;
use App\Models\Organization\Workflow\WorkflowStep;
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

test('process service advances current step and starts next step', function () {
    $user = User::factory()->create();
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

    $process = $service->advanceStep($process);
    $process->load('steps');

    $firstStep = $process->steps->firstWhere('step_order', 1);
    $secondStep = $process->steps->firstWhere('step_order', 2);

    expect($firstStep?->status)->toBe('COMPLETED')
        ->and($firstStep?->is_current)->toBeFalse()
        ->and($secondStep?->status)->toBe('IN_PROGRESS')
        ->and($secondStep?->is_current)->toBeTrue()
        ->and($process->organization_id)->toBe($secondOrganization->id);
});
