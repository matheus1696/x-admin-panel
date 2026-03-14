<?php

use App\DTOs\TimeClock\RegisterTimeClockEntryDTO;
use App\Models\Administration\User\User;
use App\Services\TimeClock\TimeClockEntryService;
use App\Validation\TimeClock\GpsAccuracyValidator;
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

test('gps accuracy validator flags imprecise coordinates when threshold is exceeded', function () {
    config()->set('time_clock.max_allowed_accuracy_meters', 50);

    expect(fn () => app(GpsAccuracyValidator::class)->validateOrFail(65.4))
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

test('time clock service records low accuracy status without blocking append only entry', function () {
    Storage::fake('public');
    config()->set('time_clock.max_allowed_accuracy_meters', 50);

    $user = User::factory()->create();
    $service = app(TimeClockEntryService::class);
    $photo = UploadedFile::fake()->image('clock.jpg');

    $entry = $service->register(new RegisterTimeClockEntryDTO(
        userId: $user->id,
        occurredAt: CarbonImmutable::now()->subMinute(),
        photo: $photo,
        latitude: -8.2835000,
        longitude: -35.9760000,
        accuracy: 80.6,
        deviceMeta: ['ip' => '127.0.0.1'],
        status: 'OK',
        locationId: null,
    ));

    expect($entry->status)->toBe('LOW_ACCURACY');
});

test('time clock service builds monthly summary with morning and afternoon slots', function () {
    $user = User::factory()->create();
    $service = app(TimeClockEntryService::class);

    \App\Models\TimeClock\TimeClockEntry::query()->create([
        'user_id' => $user->id,
        'occurred_at' => CarbonImmutable::parse('2026-03-03 08:01:00'),
        'status' => 'OK',
    ]);

    \App\Models\TimeClock\TimeClockEntry::query()->create([
        'user_id' => $user->id,
        'occurred_at' => CarbonImmutable::parse('2026-03-03 12:02:00'),
        'status' => 'OK',
    ]);

    \App\Models\TimeClock\TimeClockEntry::query()->create([
        'user_id' => $user->id,
        'occurred_at' => CarbonImmutable::parse('2026-03-03 13:10:00'),
        'status' => 'OK',
    ]);

    \App\Models\TimeClock\TimeClockEntry::query()->create([
        'user_id' => $user->id,
        'occurred_at' => CarbonImmutable::parse('2026-03-03 17:45:00'),
        'status' => 'OK',
    ]);

    $rows = $service->monthlySummary($user->id, CarbonImmutable::parse('2026-03-15'));
    $marchThird = $rows->firstWhere('day_label', '03/03');

    expect($rows)->toHaveCount(31)
        ->and($marchThird)->not->toBeNull()
        ->and($marchThird['morning_entry'])->toBe('08:01')
        ->and($marchThird['morning_exit'])->toBe('12:02')
        ->and($marchThird['afternoon_entry'])->toBe('13:10')
        ->and($marchThird['afternoon_exit'])->toBe('17:45')
        ->and($marchThird['activity_duration'])->toBe('08:36')
        ->and($marchThird['observation'])->toBeNull();
});
