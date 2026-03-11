<?php

use App\Enums\Process\ProcessStatus;
use App\Livewire\Process\ProcessIndexPage;
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

test('process show page advances to next step via http action', function () {
    $user = createProcessUser(['process.view']);
    $this->actingAs($user);

    $organizationA = OrganizationChart::query()->create([
        'title' => 'Setor A',
        'filter' => 'setor a',
        'hierarchy' => 0,
        'number_hierarchy' => 1,
        'order' => '001',
    ]);

    $organizationB = OrganizationChart::query()->create([
        'title' => 'Setor B',
        'filter' => 'setor b',
        'hierarchy' => 0,
        'number_hierarchy' => 2,
        'order' => '002',
    ]);

    $workflow = Workflow::query()->create([
        'title' => 'Fluxo de Avanco',
        'filter' => 'fluxo de avanco',
        'description' => 'Fluxo de teste para avanco',
        'is_active' => true,
    ]);

    $process = Process::query()->create([
        'title' => 'Processo Avanco Livewire',
        'description' => 'Descricao teste',
        'organization_id' => $organizationA->id,
        'workflow_id' => $workflow->id,
        'opened_by' => $user->id,
        'owner_id' => $user->id,
        'priority' => 'normal',
        'status' => ProcessStatus::IN_PROGRESS->value,
        'started_at' => now(),
    ]);

    ProcessStep::query()->create([
        'process_id' => $process->id,
        'step_order' => 1,
        'title' => 'Etapa A',
        'organization_id' => $organizationA->id,
        'deadline_days' => 2,
        'required' => true,
        'is_current' => true,
        'status' => 'IN_PROGRESS',
        'started_at' => now(),
    ]);

    ProcessStep::query()->create([
        'process_id' => $process->id,
        'step_order' => 2,
        'title' => 'Etapa B',
        'organization_id' => $organizationB->id,
        'deadline_days' => 3,
        'required' => true,
        'is_current' => false,
        'status' => 'PENDING',
    ]);

    $this->post(route('process.advance', $process->uuid))
        ->assertRedirect(route('process.show', $process->uuid));

    $process->refresh();
    $steps = ProcessStep::query()->where('process_id', $process->id)->orderBy('step_order')->get();

    expect($process->organization_id)->toBe($organizationB->id)
        ->and($steps[0]->status)->toBe('COMPLETED')
        ->and($steps[0]->is_current)->toBeFalse()
        ->and($steps[1]->status)->toBe('IN_PROGRESS')
        ->and($steps[1]->is_current)->toBeTrue();
});
