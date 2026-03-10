<?php

use App\Models\Administration\User\User;
use App\Models\TimeClock\TimeClockEntry;
use App\Services\TimeClock\TimeClockReportService;

test('time clock report returns entries by period', function () {
    $user = User::factory()->create(['is_active' => true]);

    TimeClockEntry::query()->create([
        'user_id' => $user->id,
        'occurred_at' => now()->subDay(),
        'status' => 'OK',
    ]);

    $rows = app(TimeClockReportService::class)->entriesByPeriod([
        'dateFrom' => now()->subDays(2)->toDateString(),
        'dateTo' => now()->toDateString(),
    ]);

    expect($rows)->toHaveCount(1);
});

test('time clock report lists users without entry today', function () {
    $withEntry = User::factory()->create(['name' => 'Com Registro', 'is_active' => true]);
    $withoutEntry = User::factory()->create(['name' => 'Sem Registro', 'is_active' => true]);

    TimeClockEntry::query()->create([
        'user_id' => $withEntry->id,
        'occurred_at' => now(),
        'status' => 'OK',
    ]);

    $users = app(TimeClockReportService::class)->usersWithoutEntryToday();

    expect($users->pluck('name')->all())->toContain('Sem Registro')
        ->not->toContain('Com Registro');
});

test('time clock report exports csv with expected headers', function () {
    $user = User::factory()->create(['name' => 'Export User', 'is_active' => true]);

    TimeClockEntry::query()->create([
        'user_id' => $user->id,
        'occurred_at' => now(),
        'status' => 'OK',
    ]);

    $response = app(TimeClockReportService::class)->exportEntriesCsv();
    ob_start();
    $response->sendContent();
    $content = ob_get_clean();

    expect($response->headers->get('content-type'))->toContain('text/csv')
        ->and($content)->toContain('Usuario')
        ->and($content)->toContain('Export User');
});
