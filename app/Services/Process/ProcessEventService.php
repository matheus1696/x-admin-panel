<?php

namespace App\Services\Process;

use App\Models\Process\Process;
use App\Models\Process\ProcessEvent;

class ProcessEventService
{
    public function log(Process $process, string $eventType, ?int $actorId = null, ?array $payload = null): ProcessEvent
    {
        return ProcessEvent::query()->create([
            'process_id' => $process->id,
            'event_type' => $eventType,
            'actor_id' => $actorId,
            'payload' => $payload,
            'created_at' => now(),
        ]);
    }
}

