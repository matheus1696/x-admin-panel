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

test('process show page advances to next step via livewire action', function () {
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
    $user->organizations()->attach($organizationA->id);

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

    Livewire::test(ProcessShowPage::class, ['uuid' => $process->uuid])
        ->call('openDispatchModal', 'advance')
        ->set('dispatchComment', 'Encaminhar para proxima etapa')
        ->call('confirmStepTransition')
        ->assertHasNoErrors();

    $process->refresh();
    $steps = ProcessStep::query()->where('process_id', $process->id)->orderBy('step_order')->get();

    expect($process->organization_id)->toBe($organizationB->id)
        ->and($steps[0]->status)->toBe('COMPLETED')
        ->and($steps[0]->is_current)->toBeFalse()
        ->and($steps[1]->status)->toBe('IN_PROGRESS')
        ->and($steps[1]->is_current)->toBeTrue();
});

test('process show page retreats to previous step via livewire action', function () {
    $user = createProcessUser(['process.view']);
    $this->actingAs($user);

    $organizationA = OrganizationChart::query()->create([
        'title' => 'Setor C',
        'filter' => 'setor c',
        'hierarchy' => 0,
        'number_hierarchy' => 3,
        'order' => '003',
    ]);

    $organizationB = OrganizationChart::query()->create([
        'title' => 'Setor D',
        'filter' => 'setor d',
        'hierarchy' => 0,
        'number_hierarchy' => 4,
        'order' => '004',
    ]);
    $user->organizations()->attach($organizationB->id);

    $workflow = Workflow::query()->create([
        'title' => 'Fluxo de Retorno',
        'filter' => 'fluxo de retorno',
        'description' => 'Fluxo de teste para retorno',
        'is_active' => true,
    ]);

    $process = Process::query()->create([
        'title' => 'Processo Retrocesso Livewire',
        'description' => 'Descricao teste',
        'organization_id' => $organizationB->id,
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
        'title' => 'Etapa C',
        'organization_id' => $organizationA->id,
        'deadline_days' => 2,
        'required' => true,
        'is_current' => false,
        'status' => 'COMPLETED',
        'started_at' => now()->subDay(),
        'completed_at' => now()->subHour(),
    ]);

    ProcessStep::query()->create([
        'process_id' => $process->id,
        'step_order' => 2,
        'title' => 'Etapa D',
        'organization_id' => $organizationB->id,
        'deadline_days' => 3,
        'required' => true,
        'is_current' => true,
        'status' => 'IN_PROGRESS',
        'started_at' => now(),
    ]);

    Livewire::test(ProcessShowPage::class, ['uuid' => $process->uuid])
        ->call('openDispatchModal', 'retreat')
        ->set('dispatchComment', 'Retornar para etapa anterior')
        ->call('confirmStepTransition')
        ->assertHasNoErrors();

    $process->refresh();
    $steps = ProcessStep::query()->where('process_id', $process->id)->orderBy('step_order')->get();

    expect($process->organization_id)->toBe($organizationA->id)
        ->and($steps[0]->status)->toBe('IN_PROGRESS')
        ->and($steps[0]->is_current)->toBeTrue()
        ->and($steps[1]->status)->toBe('PENDING')
        ->and($steps[1]->is_current)->toBeFalse();
});

test('process show page saves comment dispatch via modal action', function () {
    $user = createProcessUser(['process.view']);
    $this->actingAs($user);

    $process = Process::query()->create([
        'title' => 'Processo para comentario',
        'description' => 'Descricao',
        'opened_by' => $user->id,
        'owner_id' => $user->id,
        'priority' => 'normal',
        'status' => ProcessStatus::IN_PROGRESS->value,
        'started_at' => now(),
    ]);

    Livewire::test(ProcessShowPage::class, ['uuid' => $process->uuid])
        ->call('openCommentModal')
        ->set('commentText', 'Despacho de comentario no processo')
        ->call('saveComment')
        ->assertHasNoErrors();

    $event = $process->events()
        ->where('event_type', \App\Enums\Process\ProcessEventType::COMMENTED->value)
        ->latest('created_at')
        ->first();

    expect($event)->not->toBeNull()
        ->and($event->description)->toContain('Despacho de comentario no processo');
});

test('process show page assigns owner via modal action', function () {
    $user = createProcessUser(['process.view']);
    $newOwner = User::factory()->create();
    $this->actingAs($user);

    $organization = OrganizationChart::query()->create([
        'title' => 'Setor de Atribuicao',
        'filter' => 'setor de atribuicao',
        'hierarchy' => 0,
        'number_hierarchy' => 5,
        'order' => '005',
    ]);

    $newOwner->organizations()->attach($organization->id);
    $user->organizations()->attach($organization->id);

    $process = Process::query()->create([
        'title' => 'Processo para atribuicao livewire',
        'description' => 'Descricao',
        'organization_id' => $organization->id,
        'opened_by' => $user->id,
        'owner_id' => $user->id,
        'priority' => 'normal',
        'status' => ProcessStatus::IN_PROGRESS->value,
        'started_at' => now(),
    ]);

    ProcessStep::query()->create([
        'process_id' => $process->id,
        'step_order' => 1,
        'title' => 'Etapa atual',
        'organization_id' => $organization->id,
        'deadline_days' => 2,
        'required' => true,
        'is_current' => true,
        'status' => 'IN_PROGRESS',
        'started_at' => now(),
    ]);

    Livewire::test(ProcessShowPage::class, ['uuid' => $process->uuid])
        ->call('openAssignOwnerModal')
        ->set('assignedOwnerId', $newOwner->id)
        ->set('assignmentComment', 'Redistribuicao por carga de trabalho')
        ->call('assignOwner')
        ->assertHasNoErrors();

    $process->refresh();
    $event = $process->events()
        ->where('event_type', \App\Enums\Process\ProcessEventType::OWNER_ASSIGNED->value)
        ->latest('created_at')
        ->first();

    expect($process->owner_id)->toBe($newOwner->id)
        ->and($event)->not->toBeNull()
        ->and($event->description)->toContain('Redistribuicao por carga de trabalho');
});
