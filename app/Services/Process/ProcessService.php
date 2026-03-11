<?php

namespace App\Services\Process;

use App\Enums\Process\ProcessEventType;
use App\Enums\Process\ProcessStatus;
use App\Models\Administration\User\User;
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
                'events.user',
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
                'Processo criado: '.$process->title,
            );

            if ($autoStart) {
                $this->eventService->log(
                    $process,
                    ProcessEventType::STARTED->value,
                    $actorId,
                    'Processo iniciado automaticamente pela primeira etapa do fluxo.',
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

    public function advanceStep(Process $process, int $actorId, string $comment): Process
    {
        if (in_array($process->status, [ProcessStatus::CLOSED->value, ProcessStatus::CANCELLED->value], true)) {
            throw new InvalidArgumentException('Processo ja finalizado.');
        }
        $this->assertActorBelongsToCurrentStepOrganization($process, $actorId);

        $comment = trim($comment);
        if ($comment === '') {
            throw new InvalidArgumentException('Informe o motivo no despacho.');
        }

        return DB::transaction(function () use ($process, $actorId, $comment): Process {
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

            $this->eventService->log(
                $process,
                ProcessEventType::FORWARDED->value,
                $actorId,
                sprintf(
                    'Despacho: %s | Avanco de "%s" para "%s".',
                    $comment,
                    (string) $currentStep->title,
                    (string) $nextStep->title
                ),
            );

            return $process->refresh();
        });
    }

    public function retreatStep(Process $process, int $actorId, string $comment): Process
    {
        if (in_array($process->status, [ProcessStatus::CLOSED->value, ProcessStatus::CANCELLED->value], true)) {
            throw new InvalidArgumentException('Processo ja finalizado.');
        }
        $this->assertActorBelongsToCurrentStepOrganization($process, $actorId);

        $comment = trim($comment);
        if ($comment === '') {
            throw new InvalidArgumentException('Informe o motivo no despacho.');
        }

        return DB::transaction(function () use ($process, $actorId, $comment): Process {
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
            $previousStep = $steps->values()->get($currentIndex - 1);

            if (! $previousStep) {
                throw new InvalidArgumentException('Processo ja esta na primeira etapa.');
            }

            $currentStep->update([
                'is_current' => false,
                'status' => 'PENDING',
                'completed_at' => null,
            ]);

            $previousStep->update([
                'is_current' => true,
                'status' => 'IN_PROGRESS',
                'started_at' => $previousStep->started_at ?? now(),
                'completed_at' => null,
            ]);

            ProcessStep::query()
                ->where('process_id', $process->id)
                ->whereNotIn('id', [$currentStep->id, $previousStep->id])
                ->where('is_current', true)
                ->update(['is_current' => false]);

            $process->update([
                'organization_id' => $previousStep->organization_id ?? $process->organization_id,
                'status' => ProcessStatus::IN_PROGRESS->value,
                'started_at' => $process->started_at ?? now(),
            ]);

            $this->eventService->log(
                $process,
                ProcessEventType::RETURNED->value,
                $actorId,
                sprintf(
                    'Despacho: %s | Retorno de "%s" para "%s".',
                    $comment,
                    (string) $currentStep->title,
                    (string) $previousStep->title
                ),
            );

            return $process->refresh();
        });
    }

    public function comment(Process $process, int $actorId, string $comment): Process
    {
        $comment = trim($comment);
        if ($comment === '') {
            throw new InvalidArgumentException('Informe o comentario do despacho.');
        }

        return DB::transaction(function () use ($process, $actorId, $comment): Process {
            $this->eventService->log(
                $process,
                ProcessEventType::COMMENTED->value,
                $actorId,
                'Despacho de comentario: '.$comment,
            );

            return $process->refresh();
        });
    }

    public function assignOwner(Process $process, int $actorId, int $ownerId, string $comment): Process
    {
        $this->assertActorBelongsToCurrentStepOrganization($process, $actorId);

        $comment = trim($comment);
        if ($comment === '') {
            throw new InvalidArgumentException('Informe o motivo da atribuicao.');
        }

        $currentStepOrganizationId = $this->resolveCurrentStepOrganizationId($process);
        if ($currentStepOrganizationId === null) {
            throw new InvalidArgumentException('A etapa atual nao possui setor definido para atribuicao.');
        }

        $owner = User::query()->find($ownerId);
        if (! $owner) {
            throw new InvalidArgumentException('Responsavel selecionado e invalido.');
        }

        $belongsToCurrentStepOrganization = $owner->organizations()
            ->where('organization_charts.id', $currentStepOrganizationId)
            ->exists();

        if (! $belongsToCurrentStepOrganization) {
            throw new InvalidArgumentException('Responsavel deve pertencer ao setor da etapa atual.');
        }

        return DB::transaction(function () use ($process, $actorId, $owner, $comment, $currentStepOrganizationId): Process {
            $previousOwnerId = $process->owner_id;

            $process->update([
                'owner_id' => $owner->id,
            ]);

            $this->eventService->log(
                $process,
                ProcessEventType::OWNER_ASSIGNED->value,
                $actorId,
                sprintf(
                    'Despacho: %s | Responsavel alterado de #%s para %s (#%s) no setor da etapa atual (#%s).',
                    $comment,
                    (string) ($previousOwnerId ?? '-'),
                    (string) $owner->name,
                    (string) $owner->id,
                    (string) $currentStepOrganizationId
                ),
            );

            return $process->refresh();
        });
    }

    public function resolveCurrentStepOrganizationId(Process $process): ?int
    {
        $steps = ProcessStep::query()
            ->where('process_id', $process->id)
            ->orderBy('step_order')
            ->orderBy('id')
            ->get();

        if ($steps->isEmpty()) {
            return null;
        }

        $currentStep = $steps->firstWhere('is_current', true)
            ?? $steps->firstWhere('status', 'IN_PROGRESS')
            ?? $steps->firstWhere('completed_at', null)
            ?? $steps->first();

        return $currentStep?->organization_id !== null ? (int) $currentStep->organization_id : null;
    }

    public function userCanManageCurrentStepActions(Process $process, int $userId): bool
    {
        $currentStepOrganizationId = $this->resolveCurrentStepOrganizationId($process);
        if ($currentStepOrganizationId === null) {
            return false;
        }

        return User::query()
            ->whereKey($userId)
            ->whereHas('organizations', fn ($query) => $query->where('organization_charts.id', $currentStepOrganizationId))
            ->exists();
    }

    private function assertActorBelongsToCurrentStepOrganization(Process $process, int $actorId): void
    {
        if (! $this->userCanManageCurrentStepActions($process, $actorId)) {
            throw new InvalidArgumentException('Somente usuario do setor da etapa atual pode executar esta acao.');
        }
    }
}
