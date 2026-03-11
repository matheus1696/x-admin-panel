<?php

namespace App\Services\Process;

use App\Models\Process\Process;
use App\Models\Process\ProcessEvent;

class ProcessEventService
{
    public function log(Process $process, string $eventType, ?int $userId = null, ?string $description = null): ProcessEvent
    {
        $nextEventNumber = ((int) ProcessEvent::query()
            ->where('process_id', $process->id)
            ->max('event_number')) + 1;

        return ProcessEvent::query()->create([
            'process_id' => $process->id,
            'event_number' => $nextEventNumber,
            'event_type' => $eventType,
            'description' => $description,
            'user_id' => $userId,
            'created_at' => now(),
        ]);
    }
}
