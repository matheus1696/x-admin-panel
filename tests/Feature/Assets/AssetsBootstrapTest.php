<?php

use App\Enums\Assets\AssetEventType;
use App\Enums\Assets\AssetState;
use Database\Seeders\PermissionSeeder;
use Spatie\Permission\Models\Permission;

test('assets config loads', function () {
    expect(config('assets'))->toBeArray();
    expect(config('assets.stock_default_unit_id'))->toBeNull();
    expect(config('assets.patrimony_unit_id'))->toBeNull();
});

test('asset enums contain required values', function () {
    expect(AssetState::cases())
        ->toHaveCount(4);
    expect(collect(AssetState::cases())->pluck('value')->all())
        ->toBe([
            'IN_STOCK',
            'IN_USE',
            'MAINTENANCE',
            'DAMAGED',
        ]);

    expect(collect(AssetEventType::cases())->pluck('value')->all())
        ->toContain(
            'STOCK_RECEIVED',
            'RELEASED',
            'IN_USE',
            'MAINTENANCE',
            'DAMAGED',
            'RETURNED_TO_PATRIMONY',
            'TRANSFERRED',
            'AUDITED',
            'STATE_CHANGED',
        );
});

test('assets permissions seeder creates required permissions', function () {
    $this->seed(PermissionSeeder::class);

    expect(Permission::where('name', 'assets.view')->exists())->toBeTrue();
    expect(Permission::where('name', 'assets.invoices.manage')->exists())->toBeTrue();
    expect(Permission::where('name', 'assets.stock.receive')->exists())->toBeTrue();
    expect(Permission::where('name', 'assets.transfer')->exists())->toBeTrue();
    expect(Permission::where('name', 'assets.audit')->exists())->toBeTrue();
    expect(Permission::where('name', 'assets.state.change')->exists())->toBeTrue();
    expect(Permission::where('name', 'assets.return')->exists())->toBeTrue();
    expect(Permission::where('name', 'assets.reports.view')->exists())->toBeTrue();
});
