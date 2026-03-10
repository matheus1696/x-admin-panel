<?php

use App\DTOs\TimeClock\RegisterTimeClockEntryDTO;
use App\Models\Administration\User\User;
use App\Services\TimeClock\TimeClockEntryService;
use App\Validation\TimeClock\GpsRequiredValidator;
use App\Validation\TimeClock\PhotoRequiredValidator;
use Carbon\CarbonImmutable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('photo validator requires file when config demands it', function () {
    config()->set('time_clock.photo_required', true);

    expect(fn () => app(PhotoRequiredValidator::class)->validateOrFail(null))
        ->toThrow(\App\Validation\TimeClock\TimeClockValidationException::class);
});

test('gps validator requires coordinates when config demands it', function () {
    config()->set('time_clock.gps_required', true);

    expect(fn () => app(GpsRequiredValidator::class)->validateOrFail(null, null, null))
        ->toThrow(\App\Validation\TimeClock\TimeClockValidationException::class);
});

test('time clock service creates entry and stores photo', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $service = app(TimeClockEntryService::class);
    $photo = UploadedFile::fake()->image('clock.jpg');

    $entry = $service->register(new RegisterTimeClockEntryDTO(
        userId: $user->id,
        occurredAt: CarbonImmutable::now(),
        photo: $photo,
        latitude: -8.2835000,
        longitude: -35.9760000,
        accuracy: 5.2,
        deviceMeta: [
            'ip' => '127.0.0.1',
            'user_agent' => 'Pest',
        ],
        status: 'OK',
        locationId: null,
    ));

    expect($entry->user_id)->toBe($user->id)
        ->and($entry->status)->toBe('OK')
        ->and(data_get($entry->device_meta, 'ip'))->toBe('127.0.0.1')
        ->and($entry->photo_path)->not->toBeNull();

    Storage::disk('public')->assertExists($entry->photo_path);
});

test('time clock service records missing gps status without blocking append only entry', function () {
    Storage::fake('public');
    config()->set('time_clock.gps_required', true);

    $user = User::factory()->create();
    $service = app(TimeClockEntryService::class);
    $photo = UploadedFile::fake()->image('clock.jpg');

    $entry = $service->register(new RegisterTimeClockEntryDTO(
        userId: $user->id,
        occurredAt: CarbonImmutable::now()->subMinute(),
        photo: $photo,
        latitude: null,
        longitude: null,
        accuracy: null,
        deviceMeta: ['ip' => '127.0.0.1'],
        status: 'OK',
        locationId: null,
    ));

    expect($entry->status)->toBe('MISSING_GPS');
});
