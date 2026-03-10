<?php

use App\Enums\Process\ProcessEventType;
use App\Enums\Process\ProcessStatus;
use Database\Seeders\PermissionSeeder;
use Spatie\Permission\Models\Permission;

test('process config loads', function () {
    expect(config('process'))->toBeArray()
        ->and(config('process.auto_start_task'))->toBeTrue()
        ->and(config('process.timeline_append_only'))->toBeTrue()
        ->and(config('process.default_priority'))->toBe('normal');
});

test('process enums contain required values', function () {
    expect(collect(ProcessStatus::cases())->pluck('value')->all())->toBe([
        'OPEN',
        'IN_PROGRESS',
        'ON_HOLD',
        'CLOSED',
        'CANCELLED',
    ]);

    expect(collect(ProcessEventType::cases())->pluck('value')->all())->toContain(
        'PROCESS_CREATED',
        'PROCESS_STARTED',
        'PROCESS_FORWARDED',
        'PROCESS_RETURNED',
        'PROCESS_CLOSED',
        'PROCESS_CANCELLED',
    );
});

test('process permissions are created by permission seeder', function () {
    $this->seed(PermissionSeeder::class);

    expect(Permission::query()->where('name', 'process.view')->exists())->toBeTrue()
        ->and(Permission::query()->where('name', 'process.create')->exists())->toBeTrue()
        ->and(Permission::query()->where('name', 'process.manage')->exists())->toBeTrue()
        ->and(Permission::query()->where('name', 'process.close')->exists())->toBeTrue()
        ->and(Permission::query()->where('name', 'process.timeline.view')->exists())->toBeTrue();
});
