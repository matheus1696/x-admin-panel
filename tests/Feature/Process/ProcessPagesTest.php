<?php

use App\Enums\Process\ProcessStatus;
use App\Livewire\Process\ProcessIndexPage;
use App\Livewire\Process\ProcessShowPage;
use App\Models\Organization\OrganizationChart\OrganizationChart;
use App\Models\Organization\Workflow\Workflow;
use App\Models\Organization\Workflow\WorkflowStep;
use App\Models\Administration\User\User;
use App\Models\Process\Process;
use App\Models\Process\ProcessStep;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;

function createProcessUser(array $permissions): User
{
    $user = User::factory()->create();

    foreach ($permissions as $permission) {
        Permission::findOrCreate($permission, 'web');
        $user->givePermissionTo($permission);
    }

    return $user;
}

test('process index route requires view permission', function () {
    $authorized = createProcessUser(['process.view']);
    $unauthorized = User::factory()->create();

    $this->actingAs($authorized)
        ->get(route('process.index'))
        ->assertOk();

    $this->actingAs($unauthorized)
        ->get(route('process.index'))
        ->assertRedirect(route('dashboard'));
});

test('process page creates new process via livewire', function () {
    $user = createProcessUser(['process.view', 'process.create']);
    $this->actingAs($user);

    $workflow = Workflow::query()->create([
        'title' => 'Fluxo basico de criacao',
        'filter' => 'fluxo basico de criacao',
        'description' => 'Fluxo basico para criacao de processo',
        'is_active' => true,
    ]);

    Livewire::test(ProcessIndexPage::class)
        ->call('create')
        ->set('title', 'Processo Livewire')
        ->set('description', 'Criado em teste')
        ->set('workflow_id', $workflow->id)
        ->call('store')
        ->assertHasNoErrors();

    expect(Process::query()->where('title', 'Processo Livewire')->exists())->toBeTrue();
});

test('process page validates required fields on create', function () {
    $user = createProcessUser(['process.view', 'process.create']);
    $this->actingAs($user);

    Livewire::test(ProcessIndexPage::class)
        ->call('create')
        ->set('title', '')
        ->set('description', '')
        ->set('workflow_id', null)
        ->call('store')
        ->assertHasErrors([
            'title' => ['required'],
            'description' => ['required'],
            'workflow_id' => ['required'],
        ]);
});

test('process page auto starts process and sets current organization from first workflow step', function () {
    $user = createProcessUser(['process.view', 'process.create']);
    $this->actingAs($user);

    $organization = OrganizationChart::query()->create([
        'title' => 'Setor de Analise',
        'filter' => 'setor de analise',
        'hierarchy' => 0,
        'number_hierarchy' => 1,
        'order' => '001',
    ]);

    $workflow = Workflow::query()->create([
        'title' => 'Fluxo Auto Inicio',
        'filter' => 'fluxo auto inicio',
        'description' => 'Fluxo para validacao de auto inicio',
        'is_active' => true,
    ]);

    WorkflowStep::query()->create([
        'workflow_id' => $workflow->id,
        'title' => 'Etapa inicial',
        'filter' => 'etapa inicial',
        'step_order' => 1,
        'deadline_days' => 2,
        'required' => true,
        'allow_parallel' => false,
        'organization_id' => $organization->id,
    ]);

    Livewire::test(ProcessIndexPage::class)
        ->call('create')
        ->set('title', 'Processo Auto Inicio')
        ->set('description', 'Descricao do processo auto inicio')
        ->set('workflow_id', $workflow->id)
        ->call('store')
        ->assertHasNoErrors();

    $process = Process::query()
        ->where('title', 'Processo Auto Inicio')
        ->first();

    expect($process)->not->toBeNull()
        ->and($process->status)->toBe(ProcessStatus::IN_PROGRESS->value)
        ->and($process->started_at)->not->toBeNull()
        ->and($process->organization_id)->toBe($organization->id);
});

test('process show page renders workflow timeline and detail sections', function () {
    $user = createProcessUser(['process.view']);

    $organization = OrganizationChart::query()->create([
        'title' => 'Diretoria Administrativa',
        'filter' => 'diretoria administrativa',
        'hierarchy' => 0,
        'number_hierarchy' => 1,
        'order' => '001',
    ]);

    $workflow = Workflow::query()->create([
        'title' => 'Fluxo de Compras',
        'filter' => 'fluxo de compras',
        'description' => 'Fluxo padrao de compras',
        'is_active' => true,
    ]);

    $workflowStep = WorkflowStep::query()->create([
        'workflow_id' => $workflow->id,
        'title' => 'Analise inicial',
        'filter' => 'analise inicial',
        'step_order' => 1,
        'deadline_days' => 3,
        'required' => true,
        'allow_parallel' => false,
        'organization_id' => $organization->id,
    ]);

    $process = Process::query()->create([
        'title' => 'Processo de Compra de Equipamentos',
        'description' => 'Descricao detalhada do processo.',
        'organization_id' => $organization->id,
        'workflow_id' => $workflow->id,
        'opened_by' => $user->id,
        'owner_id' => $user->id,
        'priority' => 'normal',
        'status' => 'open',
    ]);

    ProcessStep::query()->create([
        'process_id' => $process->id,
        'step_order' => 1,
        'title' => 'Analise inicial',
        'organization_id' => $organization->id,
        'deadline_days' => 3,
        'required' => true,
        'is_current' => true,
    ]);

    $this->actingAs($user)
        ->get(route('process.show', $process->uuid))
        ->assertOk()
        ->assertSee('Processo')
        ->assertSee('Em andamento')
        ->assertSee('Fluxo vinculado')
        ->assertSee('Setor')
        ->assertSee('Analise inicial');
});

test('process show page advances current step to next step', function () {
    $user = createProcessUser(['process.view', 'process.manage']);
    $this->actingAs($user);

    $firstOrganization = OrganizationChart::query()->create([
        'title' => 'Setor Inicial',
        'filter' => 'setor inicial',
        'hierarchy' => 0,
        'number_hierarchy' => 1,
        'order' => '001',
    ]);

    $secondOrganization = OrganizationChart::query()->create([
        'title' => 'Setor Seguinte',
        'filter' => 'setor seguinte',
        'hierarchy' => 0,
        'number_hierarchy' => 2,
        'order' => '002',
    ]);

    $workflow = Workflow::query()->create([
        'title' => 'Fluxo de Avanco',
        'filter' => 'fluxo de avanco',
        'description' => 'Fluxo para avancar etapa',
        'is_active' => true,
    ]);

    $firstWorkflowStep = WorkflowStep::query()->create([
        'workflow_id' => $workflow->id,
        'title' => 'Etapa 1',
        'filter' => 'etapa 1',
        'step_order' => 1,
        'deadline_days' => 2,
        'required' => true,
        'allow_parallel' => false,
        'organization_id' => $firstOrganization->id,
    ]);

    $secondWorkflowStep = WorkflowStep::query()->create([
        'workflow_id' => $workflow->id,
        'title' => 'Etapa 2',
        'filter' => 'etapa 2',
        'step_order' => 2,
        'deadline_days' => 3,
        'required' => true,
        'allow_parallel' => false,
        'organization_id' => $secondOrganization->id,
    ]);

    $process = Process::query()->create([
        'title' => 'Processo em andamento',
        'description' => 'Descricao do processo',
        'organization_id' => $firstOrganization->id,
        'workflow_id' => $workflow->id,
        'opened_by' => $user->id,
        'owner_id' => $user->id,
        'priority' => 'normal',
        'status' => ProcessStatus::IN_PROGRESS->value,
        'started_at' => now(),
    ]);

    $currentProcessStep = ProcessStep::query()->create([
        'process_id' => $process->id,
        'step_order' => 1,
        'title' => 'Etapa 1',
        'organization_id' => $firstOrganization->id,
        'deadline_days' => 2,
        'required' => true,
        'is_current' => true,
        'started_at' => now()->subDay(),
        'completed_at' => null,
    ]);

    $nextProcessStep = ProcessStep::query()->create([
        'process_id' => $process->id,
        'step_order' => 2,
        'title' => 'Etapa 2',
        'organization_id' => $secondOrganization->id,
        'deadline_days' => 3,
        'required' => true,
        'is_current' => false,
        'started_at' => null,
        'completed_at' => null,
    ]);

    Livewire::test(ProcessShowPage::class, ['uuid' => $process->uuid])
        ->call('advanceStep')
        ->assertHasNoErrors();

    $currentProcessStep->refresh();
    $nextProcessStep->refresh();
    $process->refresh();

    expect($currentProcessStep->is_current)->toBeFalse()
        ->and($currentProcessStep->completed_at)->not->toBeNull()
        ->and($nextProcessStep->is_current)->toBeTrue()
        ->and($nextProcessStep->started_at)->not->toBeNull()
        ->and($process->organization_id)->toBe($secondOrganization->id);
});

test('process show page retreats current step to previous step', function () {
    $user = createProcessUser(['process.view', 'process.manage']);
    $this->actingAs($user);

    $firstOrganization = OrganizationChart::query()->create([
        'title' => 'Setor Um',
        'filter' => 'setor um',
        'hierarchy' => 0,
        'number_hierarchy' => 1,
        'order' => '001',
    ]);

    $secondOrganization = OrganizationChart::query()->create([
        'title' => 'Setor Dois',
        'filter' => 'setor dois',
        'hierarchy' => 0,
        'number_hierarchy' => 2,
        'order' => '002',
    ]);

    $workflow = Workflow::query()->create([
        'title' => 'Fluxo de Retrocesso',
        'filter' => 'fluxo de retrocesso',
        'description' => 'Fluxo para retroceder etapa',
        'is_active' => true,
    ]);

    WorkflowStep::query()->create([
        'workflow_id' => $workflow->id,
        'title' => 'Etapa 1',
        'filter' => 'etapa 1 retro',
        'step_order' => 1,
        'deadline_days' => 2,
        'required' => true,
        'allow_parallel' => false,
        'organization_id' => $firstOrganization->id,
    ]);

    WorkflowStep::query()->create([
        'workflow_id' => $workflow->id,
        'title' => 'Etapa 2',
        'filter' => 'etapa 2 retro',
        'step_order' => 2,
        'deadline_days' => 3,
        'required' => true,
        'allow_parallel' => false,
        'organization_id' => $secondOrganization->id,
    ]);

    $process = Process::query()->create([
        'title' => 'Processo para retroceder',
        'description' => 'Descricao do processo',
        'organization_id' => $secondOrganization->id,
        'workflow_id' => $workflow->id,
        'opened_by' => $user->id,
        'owner_id' => $user->id,
        'priority' => 'normal',
        'status' => ProcessStatus::IN_PROGRESS->value,
        'started_at' => now(),
    ]);

    $previousProcessStep = ProcessStep::query()->create([
        'process_id' => $process->id,
        'step_order' => 1,
        'title' => 'Etapa 1',
        'organization_id' => $firstOrganization->id,
        'deadline_days' => 2,
        'required' => true,
        'is_current' => false,
        'started_at' => now()->subDays(2),
        'completed_at' => now()->subDay(),
    ]);

    $currentProcessStep = ProcessStep::query()->create([
        'process_id' => $process->id,
        'step_order' => 2,
        'title' => 'Etapa 2',
        'organization_id' => $secondOrganization->id,
        'deadline_days' => 3,
        'required' => true,
        'is_current' => true,
        'started_at' => now()->subHours(6),
        'completed_at' => null,
    ]);

    Livewire::test(ProcessShowPage::class, ['uuid' => $process->uuid])
        ->call('retreatStep')
        ->assertHasNoErrors();

    $previousProcessStep->refresh();
    $currentProcessStep->refresh();
    $process->refresh();

    expect($previousProcessStep->is_current)->toBeTrue()
        ->and($previousProcessStep->started_at)->not->toBeNull()
        ->and($previousProcessStep->started_at->greaterThan(now()->subMinute()))->toBeTrue()
        ->and($previousProcessStep->completed_at)->toBeNull()
        ->and($currentProcessStep->is_current)->toBeFalse()
        ->and($currentProcessStep->started_at)->not->toBeNull()
        ->and($currentProcessStep->completed_at)->toBeNull()
        ->and($process->organization_id)->toBe($firstOrganization->id);
});
