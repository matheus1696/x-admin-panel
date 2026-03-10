<?php

use App\Models\Administration\User\User;
use App\Models\TimeClock\TimeClockEntry;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\TimeClockPermissionsSeeder;
use Spatie\Permission\Models\Permission;

function createTimeClockUser(array $permissions): User
{
    $user = User::factory()->create();

    foreach ($permissions as $permission) {
        Permission::findOrCreate($permission, 'web');
        $user->givePermissionTo($permission);
    }

    return $user;
}

test('time clock entry policy allows owner to view own entry', function () {
    $this->seed(PermissionSeeder::class);
    $this->seed(TimeClockPermissionsSeeder::class);

    $user = createTimeClockUser(['time_clock.view_own']);

    $entry = TimeClockEntry::query()->create([
        'user_id' => $user->id,
        'occurred_at' => now(),
        'status' => 'OK',
    ]);

    expect($user->can('view', $entry))->toBeTrue();
});

test('time clock entry policy denies unrelated user without view any permission', function () {
    $this->seed(PermissionSeeder::class);
    $this->seed(TimeClockPermissionsSeeder::class);

    $owner = createTimeClockUser(['time_clock.view_own']);
    $other = createTimeClockUser(['time_clock.view_own']);

    $entry = TimeClockEntry::query()->create([
        'user_id' => $owner->id,
        'occurred_at' => now(),
        'status' => 'OK',
    ]);

    expect($other->can('view', $entry))->toBeFalse();
});

test('time clock entry policy allows admin capabilities', function () {
    $this->seed(PermissionSeeder::class);
    $this->seed(TimeClockPermissionsSeeder::class);

    $user = createTimeClockUser(['time_clock.view_any', 'time_clock.export', 'time_clock.reports.view']);

    expect($user->can('viewAny', \App\Models\TimeClock\TimeClockEntry::class))->toBeTrue()
        ->and($user->can('export', \App\Models\TimeClock\TimeClockEntry::class))->toBeTrue()
        ->and($user->can('viewReports', \App\Models\TimeClock\TimeClockEntry::class))->toBeTrue();
});
