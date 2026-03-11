<?php

use App\Livewire\Process\ProcessDashboardPage;
use App\Models\Administration\User\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;

function createProcessDashboardUser(array $permissions): User
{
    $user = User::factory()->create();

    foreach ($permissions as $permission) {
        Permission::findOrCreate($permission, 'web');
        $user->givePermissionTo($permission);
    }

    return $user;
}

test('process dashboard route requires specific permission', function () {
    $authorized = createProcessDashboardUser(['process.dashboard.view']);
    $unauthorized = User::factory()->create();

    $this->actingAs($authorized)
        ->get(route('process.dashboard'))
        ->assertOk()
        ->assertSee('Dashboard de Processos');

    $this->actingAs($unauthorized)
        ->get(route('process.dashboard'))
        ->assertRedirect(route('dashboard'));
});

test('process dashboard livewire page renders for authorized user', function () {
    $user = createProcessDashboardUser(['process.dashboard.view']);
    $this->actingAs($user);

    Livewire::test(ProcessDashboardPage::class)
        ->assertOk()
        ->assertSee('Processos por setor atual')
        ->assertSee('Tempo medio de retorno por setor');
});
