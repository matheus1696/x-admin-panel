<?php

use App\Enums\Process\ProcessEventType;
use App\Models\Process\ProcessStatus;
use Database\Seeders\PermissionSeeder;
use Spatie\Permission\Models\Permission;

test('process config loads', function () {
    expect(config('process'))->toBeArray()
        ->and(config('process.auto_start_task'))->toBeTrue()
        ->and(config('process.timeline_append_only'))->toBeTrue()
        ->and(config('process.default_priority'))->toBe('normal');
});

test('process statuses are persisted in catalog table', function () {
    $codes = ProcessStatus::query()
        ->orderBy('sort_order')
        ->pluck('code')
        ->all();

    expect($codes)->toBe([
        ProcessStatus::IN_PROGRESS,
        ProcessStatus::CLOSED,
        ProcessStatus::CANCELLED,
    ]);

    expect(collect(ProcessEventType::cases())->pluck('value')->all())->toContain(
        'PROCESS_CREATED',
        'PROCESS_STARTED',
        'PROCESS_FORWARDED',
        'PROCESS_RETURNED',
        'PROCESS_COMMENTED',
        'PROCESS_OWNER_ASSIGNED',
        'PROCESS_CLOSED',
        'PROCESS_CANCELLED',
    );
});

test('process permissions are created by permission seeder', function () {
    $this->seed(PermissionSeeder::class);

    expect(Permission::query()->where('name', 'process.view')->exists())->toBeTrue()
        ->and(Permission::query()->where('name', 'process.create')->exists())->toBeTrue()
        ->and(Permission::query()->where('name', 'process.dashboard.view')->exists())->toBeTrue();
});
