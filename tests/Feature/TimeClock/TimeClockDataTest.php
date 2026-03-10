<?php

use App\Models\Administration\User\User;
use App\Models\TimeClock\TimeClockEntry;
use App\Models\TimeClock\TimeClockLocation;
use Illuminate\Support\Facades\Schema;

test('time clock migrations create required tables', function () {
    expect(Schema::hasTable('time_clock_entries'))->toBeTrue()
        ->and(Schema::hasTable('time_clock_locations'))->toBeTrue()
        ->and(Schema::hasColumns('time_clock_entries', [
            'user_id',
            'occurred_at',
            'photo_path',
            'latitude',
            'longitude',
            'accuracy',
            'device_meta',
            'status',
            'location_id',
        ]))->toBeTrue();
});

test('time clock models expose relations', function () {
    $user = User::factory()->create();
    $location = TimeClockLocation::query()->create([
        'name' => 'Sede Central',
        'latitude' => -8.2835000,
        'longitude' => -35.9760000,
        'radius_meters' => 150,
        'active' => true,
    ]);

    $entry = TimeClockEntry::query()->create([
        'user_id' => $user->id,
        'occurred_at' => now(),
        'status' => 'OK',
        'location_id' => $location->id,
        'device_meta' => ['ip' => '127.0.0.1'],
    ]);

    expect($entry->user->is($user))->toBeTrue()
        ->and($entry->location->is($location))->toBeTrue()
        ->and($location->entries()->count())->toBe(1)
        ->and($user->timeClockEntries()->count())->toBe(1);
});
