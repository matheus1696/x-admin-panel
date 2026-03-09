<?php

use App\Models\Administration\User\User;
use App\Models\Assets\Asset;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;

function grantAssetsPermission(User $user, string $permission): void
{
    Permission::findOrCreate($permission, 'web');
    $user->givePermissionTo($permission);
}

test('asset policy allows abilities when user has the mapped permissions', function () {
    $user = User::factory()->create();
    $asset = Asset::create([
        'code' => 'AST-POL-001',
        'description' => 'Ativo policy',
        'state' => 'IN_STOCK',
    ]);

    grantAssetsPermission($user, 'assets.view');
    grantAssetsPermission($user, 'assets.invoices.manage');
    grantAssetsPermission($user, 'assets.transfer');
    grantAssetsPermission($user, 'assets.audit');
    grantAssetsPermission($user, 'assets.state.change');
    grantAssetsPermission($user, 'assets.return');
    grantAssetsPermission($user, 'assets.reports.view');

    expect(Gate::forUser($user)->allows('viewAny', Asset::class))->toBeTrue()
        ->and(Gate::forUser($user)->allows('view', $asset))->toBeTrue()
        ->and(Gate::forUser($user)->allows('manageInvoices', Asset::class))->toBeTrue()
        ->and(Gate::forUser($user)->allows('transfer', Asset::class))->toBeTrue()
        ->and(Gate::forUser($user)->allows('audit', Asset::class))->toBeTrue()
        ->and(Gate::forUser($user)->allows('changeState', Asset::class))->toBeTrue()
        ->and(Gate::forUser($user)->allows('returnToPatrimony', Asset::class))->toBeTrue()
        ->and(Gate::forUser($user)->allows('viewReports', Asset::class))->toBeTrue();
});

test('asset policy denies abilities when user does not have permissions', function () {
    $user = User::factory()->create();
    $asset = Asset::create([
        'code' => 'AST-POL-002',
        'description' => 'Ativo sem permissao',
        'state' => 'IN_STOCK',
    ]);

    expect(Gate::forUser($user)->allows('viewAny', Asset::class))->toBeFalse()
        ->and(Gate::forUser($user)->allows('view', $asset))->toBeFalse()
        ->and(Gate::forUser($user)->allows('manageInvoices', Asset::class))->toBeFalse()
        ->and(Gate::forUser($user)->allows('returnToPatrimony', Asset::class))->toBeFalse();
});
