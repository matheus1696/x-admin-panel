<?php

namespace App\Services\Process;

use App\Enums\Process\ProcessEventType;
use App\Enums\Process\ProcessStatus;
use App\Models\Administration\User\User;
use App\Models\Organization\Workflow\Workflow;
use App\Models\Process\Process;
use App\Models\Process\ProcessStep;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ProcessService
{
    public function __construct(
        private readonly ProcessEventService $eventService,
    ) {}

    public function index(array $filters, int $userId): LengthAwarePaginator
    {
        $query = $this->accessibleProcessesQuery($userId)
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

    public function findVisibleByUuid(string $uuid, int $userId): Process
    {
        return $this->accessibleProcessesQuery($userId)
            ->with([
                'openedBy',
                'owner',
                'organization',
                'organizations',
                'workflow.workflowSteps.organization',
                'events.user',
                'steps.organization',
            ])
            ->where('uuid', $uuid)
            ->firstOrFail();
    }

    public function dashboardEntries(int $userId, int $limit = 5): Collection
    {
        return $this->accessibleProcessesQuery($userId)
            ->with(['organization', 'owner'])
            ->orderByDesc('updated_at')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    public function dashboard(array $filters): array
    {
        $processes = $this->dashboardProcessesQuery($filters)
            ->with(['organization', 'owner'])
            ->get();

        $currentSteps = $this->dashboardCurrentStepsQuery($filters)
            ->with(['organization', 'process.owner', 'process.organization'])
            ->get();

        $completedSteps = $this->dashboardCompletedStepsQuery($filters)
            ->with('organization')
            ->get();

        $statusRows = collect(ProcessStatus::cases())
            ->map(function (ProcessStatus $status) use ($processes): array {
                $total = $processes->where('status', $status->value)->count();

                return [
                    'value' => $status->value,
                    'label' => $status->label(),
                    'total' => $total,
                    'color' => $status->chartColor(),
                    'badge_class' => $status->badgeClass(),
                ];
            })
            ->values();

        $activeStatuses = [
            ProcessStatus::OPEN->value,
            ProcessStatus::IN_PROGRESS->value,
            ProcessStatus::ON_HOLD->value,
        ];

        $activeProcesses = $processes
            ->whereIn('status', $activeStatuses)
            ->values();

        $deadlineSummary = $this->buildDeadlineSummary($currentSteps);
        $sectorsCurrent = $this->buildCurrentSectorSummary($currentSteps);
        $averageSectorTimes = $this->buildAverageSectorTimes($completedSteps);
        $openingsByMonth = $this->buildMonthlyOpenings($filters);
        $overdueProcesses = $this->buildOverdueProcesses($currentSteps);
        $healthyProcesses = $this->buildHealthyProcesses($currentSteps);

        return [
            'total' => $processes->count(),
            'active_total' => $activeProcesses->count(),
            'open_total' => $processes->where('status', ProcessStatus::OPEN->value)->count(),
            'in_progress_total' => $processes->where('status', ProcessStatus::IN_PROGRESS->value)->count(),
            'on_hold_total' => $processes->where('status', ProcessStatus::ON_HOLD->value)->count(),
            'closed_total' => $processes->where('status', ProcessStatus::CLOSED->value)->count(),
            'cancelled_total' => $processes->where('status', ProcessStatus::CANCELLED->value)->count(),
            'statuses' => $statusRows,
            'deadline_summary' => $deadlineSummary,
            'current_sectors' => $sectorsCurrent,
            'average_sector_times' => $averageSectorTimes,
            'openings_by_month' => $openingsByMonth,
            'overdue_processes' => $overdueProcesses,
            'healthy_processes' => $healthyProcesses,
            'average_resolution_days' => $averageSectorTimes === []
                ? null
                : round(collect($averageSectorTimes)->avg('average_days'), 1),
        ];
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
            $this->syncOrganizations($process, $workflowSteps, $organizationId);

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

    public function userCanView(Process $process, int $userId): bool
    {
        if ((int) $process->owner_id === $userId) {
            return true;
        }

        $organizationIds = $process->relationLoaded('organizations')
            ? $process->organizations->pluck('id')->map(fn ($id): int => (int) $id)->values()
            : $process->organizations()->pluck('organization_charts.id')->map(fn ($id): int => (int) $id)->values();

        if ($organizationIds->isEmpty()) {
            return false;
        }

        return User::query()
            ->whereKey($userId)
            ->whereHas('organizations', fn ($query) => $query->whereIn('organization_charts.id', $organizationIds))
            ->exists();
    }

    private function assertActorBelongsToCurrentStepOrganization(Process $process, int $actorId): void
    {
        if (! $this->userCanManageCurrentStepActions($process, $actorId)) {
            throw new InvalidArgumentException('Somente usuario do setor da etapa atual pode executar esta acao.');
        }
    }

    private function syncOrganizations(Process $process, Collection $workflowSteps, int|string|null $fallbackOrganizationId = null): void
    {
        $organizationIds = $workflowSteps
            ->pluck('organization_id')
            ->filter()
            ->map(fn ($organizationId): int => (int) $organizationId)
            ->unique()
            ->values();

        if ($organizationIds->isEmpty() && $fallbackOrganizationId !== null) {
            $organizationIds = collect([(int) $fallbackOrganizationId]);
        }

        $process->organizations()->sync($organizationIds->all());
    }

    private function accessibleProcessesQuery(int $userId): Builder
    {
        return Process::query()
            ->where(function ($query) use ($userId): void {
                $query->where('owner_id', $userId)
                    ->orWhereHas('organizations.users', function ($organizationQuery) use ($userId): void {
                        $organizationQuery->where('users.id', $userId);
                    });
            })
            ->distinct();
    }

    private function dashboardProcessesQuery(array $filters): Builder
    {
        $query = Process::query();
        $windowStart = $this->resolveDashboardWindowStart($filters['window'] ?? '90d');

        if ($windowStart !== null) {
            $query->where('created_at', '>=', $windowStart);
        }

        if (($filters['organization_id'] ?? 'all') !== 'all') {
            $query->where('organization_id', (int) $filters['organization_id']);
        }

        return $query;
    }

    private function dashboardCurrentStepsQuery(array $filters): Builder
    {
        $query = ProcessStep::query()
            ->where('is_current', true)
            ->whereHas('process', function (Builder $processQuery) use ($filters): void {
                $this->applyDashboardFiltersToProcessQuery($processQuery, $filters);
                $processQuery->whereIn('status', [
                    ProcessStatus::OPEN->value,
                    ProcessStatus::IN_PROGRESS->value,
                    ProcessStatus::ON_HOLD->value,
                ]);
            });

        if (($filters['organization_id'] ?? 'all') !== 'all') {
            $query->where('organization_id', (int) $filters['organization_id']);
        }

        return $query;
    }

    private function dashboardCompletedStepsQuery(array $filters): Builder
    {
        $query = ProcessStep::query()
            ->whereNotNull('organization_id')
            ->whereNotNull('started_at')
            ->whereNotNull('completed_at')
            ->whereHas('process', function (Builder $processQuery) use ($filters): void {
                $this->applyDashboardFiltersToProcessQuery($processQuery, $filters);
            });

        if (($filters['organization_id'] ?? 'all') !== 'all') {
            $query->where('organization_id', (int) $filters['organization_id']);
        }

        return $query;
    }

    private function applyDashboardFiltersToProcessQuery(Builder $query, array $filters): void
    {
        $windowStart = $this->resolveDashboardWindowStart($filters['window'] ?? '90d');

        if ($windowStart !== null) {
            $query->where('created_at', '>=', $windowStart);
        }

        if (($filters['organization_id'] ?? 'all') !== 'all') {
            $query->where('organization_id', (int) $filters['organization_id']);
        }
    }

    private function resolveDashboardWindowStart(string $window): mixed
    {
        return match ($window) {
            '30d' => now()->subDays(30)->startOfDay(),
            '90d' => now()->subDays(90)->startOfDay(),
            '180d' => now()->subDays(180)->startOfDay(),
            '365d' => now()->subDays(365)->startOfDay(),
            default => null,
        };
    }

    private function buildDeadlineSummary(Collection $currentSteps): array
    {
        $overdue = 0;
        $onTime = 0;
        $withoutDeadline = 0;

        foreach ($currentSteps as $step) {
            $dueAt = $this->resolveStepDueAt($step);

            if ($dueAt === null) {
                $withoutDeadline++;

                continue;
            }

            if ($dueAt->lt(now())) {
                $overdue++;
            } else {
                $onTime++;
            }
        }

        return [
            'on_time' => $onTime,
            'overdue' => $overdue,
            'without_deadline' => $withoutDeadline,
        ];
    }

    private function buildCurrentSectorSummary(Collection $currentSteps): array
    {
        return $currentSteps
            ->groupBy(fn (ProcessStep $step): string => $step->organization?->title ?? 'Nao definido')
            ->map(fn (Collection $steps, string $label): array => [
                'label' => $label,
                'total' => $steps->count(),
            ])
            ->sortByDesc('total')
            ->values()
            ->all();
    }

    private function buildAverageSectorTimes(Collection $completedSteps): array
    {
        return $completedSteps
            ->groupBy(fn (ProcessStep $step): string => $step->organization?->title ?? 'Nao definido')
            ->map(function (Collection $steps, string $label): array {
                $averageHours = round($steps->avg(
                    fn (ProcessStep $step): float => (float) $step->started_at->diffInHours($step->completed_at)
                ), 1);

                return [
                    'label' => $label,
                    'average_hours' => $averageHours,
                    'average_days' => round($averageHours / 24, 1),
                    'formatted' => $averageHours >= 24
                        ? round($averageHours / 24, 1).' dia(s)'
                        : $averageHours.' h',
                    'total_steps' => $steps->count(),
                ];
            })
            ->sortByDesc('average_hours')
            ->values()
            ->all();
    }

    private function buildMonthlyOpenings(array $filters): array
    {
        return $this->dashboardProcessesQuery($filters)
            ->get(['created_at'])
            ->groupBy(fn (Process $process): string => $process->created_at->copy()->startOfMonth()->format('Y-m-01'))
            ->map(fn (Collection $processes, string $monthStart): array => [
                'sort_key' => $monthStart,
                'label' => \Illuminate\Support\Carbon::parse($monthStart)->format('m/Y'),
                'total' => $processes->count(),
            ])
            ->sortBy('sort_key')
            ->map(fn (array $item): array => [
                'label' => $item['label'],
                'total' => $item['total'],
            ])
            ->values()
            ->all();
    }

    private function buildOverdueProcesses(Collection $currentSteps): array
    {
        return $currentSteps
            ->map(function (ProcessStep $step): ?array {
                $dueAt = $this->resolveStepDueAt($step);

                if ($dueAt === null || ! $dueAt->lt(now())) {
                    return null;
                }

                return [
                    'uuid' => $step->process?->uuid,
                    'code' => $step->process?->code ?? '-',
                    'title' => $step->process?->title ?? 'Processo sem titulo',
                    'sector' => $step->organization?->title ?? 'Nao definido',
                    'owner' => $step->process?->owner?->name ?? 'Nao atribuido',
                    'due_at' => $dueAt,
                    'delay_days' => $dueAt->diffInDays(now()),
                ];
            })
            ->filter()
            ->sortByDesc('delay_days')
            ->take(8)
            ->values()
            ->all();
    }

    private function buildHealthyProcesses(Collection $currentSteps): array
    {
        return $currentSteps
            ->map(function (ProcessStep $step): array {
                $dueAt = $this->resolveStepDueAt($step);

                return [
                    'uuid' => $step->process?->uuid,
                    'code' => $step->process?->code ?? '-',
                    'title' => $step->process?->title ?? 'Processo sem titulo',
                    'sector' => $step->organization?->title ?? 'Nao definido',
                    'owner' => $step->process?->owner?->name ?? 'Nao atribuido',
                    'due_at' => $dueAt,
                    'has_deadline' => $dueAt !== null,
                ];
            })
            ->filter(function (array $item): bool {
                return $item['due_at'] === null || $item['due_at']->gte(now());
            })
            ->sortBy(fn (array $item) => $item['due_at'] ?? now()->addYears(5))
            ->take(8)
            ->values()
            ->all();
    }

    private function resolveStepDueAt(ProcessStep $step): mixed
    {
        if ($step->started_at === null || $step->deadline_days === null) {
            return null;
        }

        return $step->started_at->copy()->addDays((int) $step->deadline_days);
    }
}
