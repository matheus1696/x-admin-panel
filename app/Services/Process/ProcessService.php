<?php

namespace App\Services\Process;

use App\Enums\Process\ProcessEventType;
use App\Enums\Process\ProcessStatus;
use App\Models\Organization\Workflow\Workflow;
use App\Models\Process\Process;
use App\Models\Process\ProcessStep;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
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
                'steps.organization',
            ])
            ->where('uuid', $uuid)
            ->firstOrFail();
    }

    public function open(array $data, int $actorId): Process
    {
        return DB::transaction(function () use ($data, $actorId): Process {
            $workflowId = isset($data['workflow_id']) ? (int) $data['workflow_id'] : null;
            $organizationId = $data['organization_id'] ?? null;
            $workflowSteps = collect();
            $firstWorkflowStep = null;

            if ($workflowId !== null) {
                $workflowSteps = $this->workflowSteps($workflowId);
                $firstWorkflowStep = $workflowSteps->first();

                if ($firstWorkflowStep?->organization_id !== null) {
                    $organizationId = (int) $firstWorkflowStep->organization_id;
                }
            }

            $autoStart = $firstWorkflowStep !== null;

            $process = Process::query()->create([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'organization_id' => $organizationId,
                'workflow_id' => $workflowId,
                'opened_by' => $actorId,
                'owner_id' => $data['owner_id'] ?? $actorId,
                'priority' => $data['priority'] ?? config('process.default_priority', 'normal'),
                'status' => $autoStart ? ProcessStatus::IN_PROGRESS->value : ProcessStatus::OPEN->value,
                'started_at' => $autoStart ? now() : null,
            ]);

            $this->createProcessSteps($process, $workflowSteps, (int) ($firstWorkflowStep?->step_order ?? 0));

            $this->eventService->log(
                $process,
                ProcessEventType::CREATED->value,
                $actorId,
                [
                    'title' => $process->title,
                    'workflow_id' => $process->workflow_id,
                    'organization_id' => $process->organization_id,
                    'auto_started' => $autoStart,
                ],
            );

            if ($autoStart) {
                $this->eventService->log(
                    $process,
                ProcessEventType::STARTED->value,
                $actorId,
                [
                    'auto_started' => true,
                ],
            );
            }

            return $process->refresh();
        });
    }

    private function workflowSteps(int $workflowId): Collection
    {
        return Workflow::query()
            ->whereKey($workflowId)
            ->first()?->workflowSteps()
            ->orderBy('step_order')
            ->orderBy('id')
            ->get() ?? collect();
    }

    private function createProcessSteps(Process $process, Collection $workflowSteps, int $currentStepOrder): void
    {
        if ($workflowSteps->isEmpty()) {
            return;
        }

        foreach ($workflowSteps as $step) {
            ProcessStep::query()->create([
                'process_id' => $process->id,
                'step_order' => (int) $step->step_order,
                'title' => (string) $step->title,
                'organization_id' => $step->organization_id,
                'deadline_days' => $step->deadline_days,
                'required' => (bool) $step->required,
                'is_current' => (int) $step->step_order === $currentStepOrder,
                'started_at' => (int) $step->step_order === $currentStepOrder ? now() : null,
                'completed_at' => null,
            ]);
        }
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

    public function forward(Process $process, int $actorId, string $comment): Process
    {
        return $this->move($process, $actorId, $comment, direction: 'forward');
    }

    public function backward(Process $process, int $actorId, string $comment): Process
    {
        return $this->move($process, $actorId, $comment, direction: 'backward');
    }

    public function advanceStep(Process $process): Process
    {
        if (in_array($process->status, [ProcessStatus::CLOSED->value, ProcessStatus::CANCELLED->value], true)) {
            throw new InvalidArgumentException('Process is already finalized.');
        }

        return DB::transaction(function () use ($process): Process {
            $steps = $this->processSteps($process->id);

            if ($steps->count() < 2) {
                throw new InvalidArgumentException('Workflow must have at least two steps.');
            }

            $currentStep = $this->resolveCurrentStep($steps);
            if (! $currentStep) {
                throw new InvalidArgumentException('Process has no valid step state.');
            }

            $currentIndex = $steps->search(fn (ProcessStep $step): bool => (int) $step->id === (int) $currentStep->id);
            $nextStep = $steps->values()->get((int) $currentIndex + 1);

            if (! $nextStep) {
                throw new InvalidArgumentException('Process is already at the final workflow step.');
            }

            ProcessStep::query()
                ->where('process_id', $process->id)
                ->update(['is_current' => false]);

            $currentStep->update([
                'is_current' => false,
                'completed_at' => $currentStep->completed_at ?? now(),
            ]);

            $nextStep->update([
                'is_current' => true,
                'started_at' => $nextStep->started_at ?? now(),
                'completed_at' => null,
            ]);

            $process->update([
                'organization_id' => $nextStep->organization_id ?? $process->organization_id,
                'status' => ProcessStatus::IN_PROGRESS->value,
                'started_at' => $process->started_at ?? now(),
            ]);

            return $process->refresh();
        });
    }

    public function retreatStep(Process $process): Process
    {
        if (in_array($process->status, [ProcessStatus::CLOSED->value, ProcessStatus::CANCELLED->value], true)) {
            throw new InvalidArgumentException('Process is already finalized.');
        }

        return DB::transaction(function () use ($process): Process {
            $steps = $this->processSteps($process->id);

            if ($steps->count() < 2) {
                throw new InvalidArgumentException('Workflow must have at least two steps.');
            }

            $currentStep = $this->resolveCurrentStep($steps);
            if (! $currentStep) {
                throw new InvalidArgumentException('Process has no valid step state.');
            }

            $currentIndex = $steps->search(fn (ProcessStep $step): bool => (int) $step->id === (int) $currentStep->id);
            $previousStep = $steps->values()->get((int) $currentIndex - 1);

            if (! $previousStep) {
                throw new InvalidArgumentException('Process is already at the first workflow step.');
            }

            ProcessStep::query()
                ->where('process_id', $process->id)
                ->update(['is_current' => false]);

            $currentStep->update([
                'is_current' => false,
                'completed_at' => null,
            ]);

            $previousStep->update([
                'is_current' => true,
                'started_at' => now(),
                'completed_at' => null,
            ]);

            $process->update([
                'organization_id' => $previousStep->organization_id ?? $process->organization_id,
                'status' => ProcessStatus::IN_PROGRESS->value,
                'started_at' => $process->started_at ?? now(),
            ]);

            return $process->refresh();
        });
    }

    private function move(Process $process, int $actorId, string $comment, string $direction): Process
    {
        if (in_array($process->status, [ProcessStatus::CLOSED->value, ProcessStatus::CANCELLED->value], true)) {
            throw new InvalidArgumentException('Process is already finalized.');
        }

        if ($process->workflow_id === null) {
            throw new InvalidArgumentException('Process has no workflow linked.');
        }

        $comment = trim($comment);
        if ($comment === '') {
            throw new InvalidArgumentException('Comment is required to move process.');
        }

        return DB::transaction(function () use ($process, $actorId, $comment, $direction): Process {
            $steps = $this->processSteps($process->id);

            if ($steps->count() < 2) {
                throw new InvalidArgumentException('Workflow must have at least two steps.');
            }

            $currentStep = $steps->firstWhere('is_current', true);
            if (! $currentStep) {
                $currentStep = $steps->first();
            }

            $currentIndex = $steps->search(fn (ProcessStep $step): bool => (int) $step->id === (int) $currentStep->id);
            $targetIndex = $direction === 'forward' ? ((int) $currentIndex + 1) : ((int) $currentIndex - 1);
            $targetStep = $steps->values()->get($targetIndex);

            if (! $targetStep) {
                throw new InvalidArgumentException(
                    $direction === 'forward'
                        ? 'Process is already at the final workflow step.'
                        : 'Process is already at the first workflow step.'
                );
            }

            $currentStep->update([
                'is_current' => false,
                'completed_at' => $direction === 'forward' ? now() : null,
            ]);

            $targetStep->update([
                'is_current' => true,
                'started_at' => now(),
                'completed_at' => null,
            ]);

            $process->update([
                'organization_id' => $targetStep->organization_id ?? $process->organization_id,
                'status' => $process->status === ProcessStatus::OPEN->value ? ProcessStatus::IN_PROGRESS->value : $process->status,
                'started_at' => $process->started_at ?? now(),
            ]);

            $this->eventService->log(
                $process,
                $direction === 'forward' ? ProcessEventType::FORWARDED->value : ProcessEventType::RETURNED->value,
                $actorId,
                [
                    'comment' => $comment,
                    'from_step_id' => $currentStep?->id,
                    'from_step_title' => $currentStep?->title,
                    'to_step_id' => $targetStep->id,
                    'to_step_title' => $targetStep->title,
                    'to_organization_id' => $targetStep->organization_id,
                    'to_organization_title' => $targetStep->organization?->title,
                    'direction' => $direction,
                ],
            );

            return $process->refresh();
        });
    }

    private function processSteps(int $processId): Collection
    {
        return ProcessStep::query()
            ->with('organization')
            ->where('process_id', $processId)
            ->orderBy('step_order')
            ->orderBy('id')
            ->get();
    }

    private function resolveCurrentStep(Collection $steps): ?ProcessStep
    {
        $current = $steps->firstWhere('is_current', true);
        if ($current instanceof ProcessStep) {
            return $current;
        }

        $firstUncompleted = $steps->first(fn (ProcessStep $step): bool => $step->completed_at === null);
        if ($firstUncompleted instanceof ProcessStep) {
            return $firstUncompleted;
        }

        return $steps->last();
    }
}
