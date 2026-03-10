<?php

use App\Livewire\TimeClock\RegisterEntry;
use App\Models\Administration\User\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\TimeClockPermissionsSeeder;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;

function createTimeClockPageUser(array $permissions): User
{
    $user = User::factory()->create(['is_active' => true]);

    foreach ($permissions as $permission) {
        Permission::findOrCreate($permission, 'web');
        $user->givePermissionTo($permission);
    }

    return $user;
}

beforeEach(function () {
    $this->seed(PermissionSeeder::class);
    $this->seed(TimeClockPermissionsSeeder::class);
});

test('time clock register page requires permission', function () {
    $authorized = createTimeClockPageUser(['time_clock.register']);
    $unauthorized = User::factory()->create();

    $this->actingAs($authorized)
        ->get(route('time-clock.register'))
        ->assertOk();

    $this->actingAs($unauthorized)
        ->get(route('time-clock.register'))
        ->assertRedirect(route('dashboard'));
});

test('time clock register livewire stores entry', function () {
    \Illuminate\Support\Facades\Storage::fake('public');

    $user = createTimeClockPageUser(['time_clock.register']);
    $this->actingAs($user);

    Livewire::test(RegisterEntry::class)
        ->set('photo', \Illuminate\Http\UploadedFile::fake()->image('point.jpg'))
        ->set('latitude', -8.2835000)
        ->set('longitude', -35.9760000)
        ->set('accuracy', 4.5)
        ->call('register')
        ->assertHasNoErrors();

    expect(\App\Models\TimeClock\TimeClockEntry::query()->where('user_id', $user->id)->exists())->toBeTrue();
});

test('time clock management pages load with proper permissions', function () {
    $user = createTimeClockPageUser([
        'time_clock.view_own',
        'time_clock.view_any',
        'time_clock.reports.view',
        'time_clock.locations.manage',
    ]);

    $this->actingAs($user)
        ->get(route('time-clock.my-entries'))
        ->assertOk();

    $this->actingAs($user)
        ->get(route('time-clock.entries.index'))
        ->assertOk();

    $this->actingAs($user)
        ->get(route('time-clock.reports.index'))
        ->assertOk();

    $this->actingAs($user)
        ->get(route('time-clock.locations.index'))
        ->assertOk();
});
