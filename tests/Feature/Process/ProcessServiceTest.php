<?php

use App\Enums\Process\ProcessStatus;
use App\Models\Administration\User\User;
use App\Models\Process\Process;
use App\Services\Process\ProcessService;

test('process service opens process and logs creation event', function () {
    $user = User::factory()->create();
    $service = app(ProcessService::class);

    $process = $service->open([
        'title' => 'Processo de Contratacao',
        'description' => 'Descricao teste',
        'priority' => 'high',
    ], $user->id);

    expect($process->title)->toBe('Processo de Contratacao')
        ->and($process->status)->toBe(ProcessStatus::OPEN->value)
        ->and($process->code)->toStartWith('PRC')
        ->and($process->events()->count())->toBe(1);
});

test('process service starts and closes process with events', function () {
    $user = User::factory()->create();
    $service = app(ProcessService::class);

    $process = $service->open([
        'title' => 'Processo de Auditoria',
        'description' => null,
    ], $user->id);

    $process = $service->start($process, $user->id);
    $process = $service->close($process, $user->id, 'Processo concluido');

    expect($process->status)->toBe(ProcessStatus::CLOSED->value)
        ->and($process->closed_at)->not->toBeNull()
        ->and($process->events()->count())->toBe(3);
});

test('process service cancels process with required note', function () {
    $user = User::factory()->create();
    $service = app(ProcessService::class);

    $process = $service->open([
        'title' => 'Processo de Cancelamento',
    ], $user->id);

    $process = $service->cancel($process, $user->id, 'Solicitacao interrompida');

    expect($process->status)->toBe(ProcessStatus::CANCELLED->value)
        ->and($process->closed_at)->not->toBeNull()
        ->and($process->events()->count())->toBe(2);
});

test('process service rejects start when process is already in progress', function () {
    $user = User::factory()->create();
    $service = app(ProcessService::class);

    $process = Process::query()->create([
        'title' => 'Processo Ja Iniciado',
        'opened_by' => $user->id,
        'owner_id' => $user->id,
        'priority' => 'normal',
        'status' => ProcessStatus::IN_PROGRESS->value,
    ]);

    expect(fn () => $service->start($process, $user->id))
        ->toThrow(\InvalidArgumentException::class);
});
