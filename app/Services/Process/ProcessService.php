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
                'status' => (int) $step->step_order === $currentStepOrder ? 'IN_PROGRESS' : 'PENDING',
                'started_at' => (int) $step->step_order === $currentStepOrder ? now() : null,
                'completed_at' => null,
            ]);
        }
    }

    public function advanceStep(Process $process): Process
    {
        if (in_array($process->status, [ProcessStatus::CLOSED->value, ProcessStatus::CANCELLED->value], true)) {
            throw new InvalidArgumentException('Processo ja finalizado.');
        }

        return DB::transaction(function () use ($process): Process {
            $steps = ProcessStep::query()
                ->where('process_id', $process->id)
                ->orderBy('step_order')
                ->orderBy('id')
                ->get();

            if ($steps->count() < 2) {
                throw new InvalidArgumentException('Fluxo precisa ter pelo menos duas etapas.');
            }

            $currentStep = $steps->firstWhere('is_current', true)
                ?? $steps->firstWhere('status', 'IN_PROGRESS')
                ?? $steps->firstWhere('completed_at', null)
                ?? $steps->first();

            $currentIndex = (int) $steps->search(
                fn (ProcessStep $step): bool => (int) $step->id === (int) $currentStep->id
            );
            $nextStep = $steps->values()->get($currentIndex + 1);

            if (! $nextStep) {
                throw new InvalidArgumentException('Processo ja esta na ultima etapa.');
            }

            $currentStep->update([
                'is_current' => false,
                'status' => 'COMPLETED',
                'completed_at' => $currentStep->completed_at ?? now(),
            ]);

            $nextStep->update([
                'is_current' => true,
                'status' => 'IN_PROGRESS',
                'started_at' => $nextStep->started_at ?? now(),
                'completed_at' => null,
            ]);

            ProcessStep::query()
                ->where('process_id', $process->id)
                ->whereNotIn('id', [$currentStep->id, $nextStep->id])
                ->where('is_current', true)
                ->update(['is_current' => false]);

            $process->update([
                'organization_id' => $nextStep->organization_id ?? $process->organization_id,
                'status' => ProcessStatus::IN_PROGRESS->value,
                'started_at' => $process->started_at ?? now(),
            ]);

            return $process->refresh();
        });
    }
}
