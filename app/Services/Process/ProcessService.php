<?php

namespace App\Services\Process;

use App\Enums\Process\ProcessEventType;
use App\Enums\Process\ProcessStatus;
use App\Models\Process\Process;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ProcessService
{
    public function __construct(
        private readonly ProcessEventService $eventService,
    ) {
    }

    public function index(array $filters): LengthAwarePaginator
    {
        $query = Process::query()
            ->with(['openedBy', 'owner', 'organization', 'workflow']);

        if (! empty($filters['title'])) {
            $query->where('title', 'like', '%'.$filters['title'].'%');
        }

        if (($filters['status'] ?? 'all') !== 'all') {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['organization_id'])) {
            $query->where('organization_id', (int) $filters['organization_id']);
        }

        return $query
            ->orderByDesc('created_at')
            ->paginate((int) ($filters['perPage'] ?? 10));
    }

    public function findByUuid(string $uuid): Process
    {
        return Process::query()
            ->with([
                'openedBy',
                'owner',
                'organization',
                'workflow.workflowSteps.organization',
                'events.actor',
            ])
            ->where('uuid', $uuid)
            ->firstOrFail();
    }

    public function open(array $data, int $actorId): Process
    {
        return DB::transaction(function () use ($data, $actorId): Process {
            $process = Process::query()->create([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'organization_id' => $data['organization_id'] ?? null,
                'workflow_id' => $data['workflow_id'] ?? null,
                'opened_by' => $actorId,
                'owner_id' => $data['owner_id'] ?? $actorId,
                'priority' => $data['priority'] ?? config('process.default_priority', 'normal'),
                'status' => ProcessStatus::OPEN->value,
            ]);

            $this->eventService->log(
                $process,
                ProcessEventType::CREATED->value,
                $actorId,
                [
                    'title' => $process->title,
                    'workflow_id' => $process->workflow_id,
                    'organization_id' => $process->organization_id,
                ],
            );

            return $process->refresh();
        });
    }

    public function start(Process $process, int $actorId, ?string $note = null): Process
    {
        if ($process->status !== ProcessStatus::OPEN->value) {
            throw new InvalidArgumentException('Only open processes can be started.');
        }

        return DB::transaction(function () use ($process, $actorId, $note): Process {
            $process->update([
                'status' => ProcessStatus::IN_PROGRESS->value,
                'started_at' => now(),
            ]);

            $this->eventService->log(
                $process,
                ProcessEventType::STARTED->value,
                $actorId,
                ['note' => $note],
            );

            return $process->refresh();
        });
    }

    public function close(Process $process, int $actorId, ?string $note = null): Process
    {
        if (! in_array($process->status, [ProcessStatus::OPEN->value, ProcessStatus::IN_PROGRESS->value, ProcessStatus::ON_HOLD->value], true)) {
            throw new InvalidArgumentException('Process cannot be closed from current status.');
        }

        return DB::transaction(function () use ($process, $actorId, $note): Process {
            $process->update([
                'status' => ProcessStatus::CLOSED->value,
                'closed_at' => now(),
            ]);

            $this->eventService->log(
                $process,
                ProcessEventType::CLOSED->value,
                $actorId,
                ['note' => $note],
            );

            return $process->refresh();
        });
    }

    public function cancel(Process $process, int $actorId, string $note): Process
    {
        if (in_array($process->status, [ProcessStatus::CLOSED->value, ProcessStatus::CANCELLED->value], true)) {
            throw new InvalidArgumentException('Process is already finalized.');
        }

        return DB::transaction(function () use ($process, $actorId, $note): Process {
            $process->update([
                'status' => ProcessStatus::CANCELLED->value,
                'closed_at' => now(),
            ]);

            $this->eventService->log(
                $process,
                ProcessEventType::CANCELLED->value,
                $actorId,
                ['note' => $note],
            );

            return $process->refresh();
        });
    }
}
