<?php

use App\Enums\TimeClock\TimeClockEntryStatus;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\TimeClockPermissionsSeeder;
use Spatie\Permission\Models\Permission;

test('time clock config loads', function () {
    expect(config('time_clock'))->toBeArray()
        ->and(config('time_clock.photo_required'))->toBeTrue()
        ->and(config('time_clock.gps_required'))->toBeTrue()
        ->and(config('time_clock.max_allowed_accuracy_meters'))->toBe(50)
        ->and(config('time_clock.validate_location_enabled'))->toBeFalse()
        ->and(config('time_clock.default_location_radius_meters'))->toBe(150);
});

test('time clock enum values exist', function () {
    expect(collect(TimeClockEntryStatus::cases())->pluck('value')->all())->toBe([
        'OK',
        'MISSING_GPS',
        'MISSING_PHOTO',
        'LOW_ACCURACY',
    ]);
});

test('time clock permissions seeder creates permissions', function () {
    $this->seed(PermissionSeeder::class);
    $this->seed(TimeClockPermissionsSeeder::class);

    expect(Permission::query()->where('name', 'time_clock.register')->exists())->toBeTrue()
        ->and(Permission::query()->where('name', 'time_clock.view_own')->exists())->toBeTrue()
        ->and(Permission::query()->where('name', 'time_clock.view_any')->exists())->toBeTrue()
        ->and(Permission::query()->where('name', 'time_clock.reports.view')->exists())->toBeTrue()
        ->and(Permission::query()->where('name', 'time_clock.export')->exists())->toBeTrue()
        ->and(Permission::query()->where('name', 'time_clock.locations.manage')->exists())->toBeTrue()
        ->and(Permission::query()->where('name', 'time_clock.settings.manage')->exists())->toBeTrue();
});
