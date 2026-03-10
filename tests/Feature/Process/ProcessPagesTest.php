<?php

use App\Livewire\Process\ProcessIndexPage;
use App\Models\Organization\OrganizationChart\OrganizationChart;
use App\Models\Organization\Workflow\Workflow;
use App\Models\Organization\Workflow\WorkflowStep;
use App\Models\Administration\User\User;
use App\Models\Process\Process;
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

    Livewire::test(ProcessIndexPage::class)
        ->call('create')
        ->set('title', 'Processo Livewire')
        ->set('description', 'Criado em teste')
        ->call('store')
        ->assertHasNoErrors();

    expect(Process::query()->where('title', 'Processo Livewire')->exists())->toBeTrue();
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

    WorkflowStep::query()->create([
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

    $this->actingAs($user)
        ->get(route('process.show', $process->uuid))
        ->assertOk()
        ->assertSee('Processo')
        ->assertSee('Novo comentario')
        ->assertSee('Documentos relacionados')
        ->assertSee('Analise inicial');
});
