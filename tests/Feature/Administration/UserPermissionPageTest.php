<?php

use App\Livewire\Administration\User\UserPermissionPage;
use App\Models\Administration\User\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

function grantUserPermission(User $user, string $permission): void
{
    Permission::findOrCreate($permission, 'web');
    $user->givePermissionTo($permission);
}

test('user permission page renders for authorized users', function () {
    $manager = User::factory()->create();
    $targetUser = User::factory()->create();

    grantUserPermission($manager, 'administration.manage.users.permissions');

    $this->actingAs($manager)
        ->get(route('administration.manage.users.permissions', $targetUser->id))
        ->assertOk()
        ->assertSee('Permissoes do Usuario');
});

test('user permission page copies roles and permissions from shadow user and saves', function () {
    $manager = User::factory()->create();
    $targetUser = User::factory()->create();
    $shadowUser = User::factory()->create();

    grantUserPermission($manager, 'administration.manage.users.permissions');
    grantUserPermission($manager, 'administration.manage.users');

    Permission::findOrCreate('administration.manage.suppliers', 'web');
    Permission::findOrCreate('administration.manage.products', 'web');
    Permission::findOrCreate('administration.manage.task', 'web');
    Permission::findOrCreate('administration.manage.product-types', 'web');

    $profile = Role::firstOrCreate(
        ['name' => 'profile-admin-tests', 'guard_name' => 'web'],
        ['type' => 'Perfil Teste', 'translation' => 'Perfil Teste']
    );
    $profile->syncPermissions(['administration.manage.products', 'administration.manage.product-types']);

    $targetUser->syncPermissions(['administration.manage.task']);
    $shadowUser->syncRoles([$profile->name]);
    $shadowUser->syncPermissions(['administration.manage.suppliers']);

    $this->actingAs($manager);

    Livewire::test(UserPermissionPage::class, ['id' => $targetUser->id])
        ->set('shadowUserId', (string) $shadowUser->id)
        ->call('copyFromShadowUser')
        ->call('save')
        ->assertRedirect(route('administration.manage.users'));

    $targetUser->refresh();

    expect($targetUser->hasRole($profile->name))->toBeTrue()
        ->and($targetUser->hasPermissionTo('administration.manage.product-types'))->toBeTrue()
        ->and($targetUser->hasPermissionTo('administration.manage.products'))->toBeTrue()
        ->and($targetUser->hasPermissionTo('administration.manage.suppliers'))->toBeTrue()
        ->and($targetUser->hasDirectPermission('administration.manage.products'))->toBeFalse()
        ->and($targetUser->hasDirectPermission('administration.manage.suppliers'))->toBeTrue();

    expect($targetUser->hasPermissionTo('administration.manage.suppliers'))->toBeTrue()
        ->and($targetUser->hasPermissionTo('administration.manage.products'))->toBeTrue()
        ->and($targetUser->hasPermissionTo('administration.manage.task'))->toBeFalse();
});
