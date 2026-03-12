<?php

namespace Database\Seeders\Process;

use App\Enums\Process\ProcessEventType;
use App\Models\Process\ProcessStatus;
use App\Models\Administration\User\User;
use App\Models\Organization\Workflow\Workflow;
use App\Models\Organization\Workflow\WorkflowStep;
use App\Models\Process\Process;
use App\Models\Process\ProcessStep;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ProcessSeeder extends Seeder
{
    private const MONTHS_BACK = 12;

    private const MIN_PROCESSES_PER_MONTH = 1;

    private const MAX_PROCESSES_PER_MONTH = 5;

    public function run(): void
    {
        $workflow = Workflow::query()
            ->with(['workflowSteps' => fn ($query) => $query->orderBy('step_order')->orderBy('id')])
            ->first();

        if (! $workflow || $workflow->workflowSteps->isEmpty()) {
            return;
        }

        $actors = $this->ensureActors($workflow->workflowSteps);
        $scenarios = $this->scenarios($workflow->workflowSteps->count());

        foreach ($scenarios as $scenario) {
            $this->seedScenario($workflow, $workflow->workflowSteps, $actors, $scenario);
        }
    }

    /**
     * @return array<string, \App\Models\Administration\User\User>
     */
    private function ensureActors(Collection $workflowSteps): array
    {
        $actors = [
            'admin@example.com' => $this->ensureUser('Admin User', 'admin@example.com'),
            'test@example.com' => $this->ensureUser('Test User', 'test@example.com'),
            'analista.processos@example.com' => $this->ensureUser('Ana Processos', 'analista.processos@example.com'),
            'compras@example.com' => $this->ensureUser('Bruno Compras', 'compras@example.com'),
            'planejamento@example.com' => $this->ensureUser('Carla Planejamento', 'planejamento@example.com'),
            'gestao.contratos@example.com' => $this->ensureUser('Diego Contratos', 'gestao.contratos@example.com'),
        ];

        $stepOrganizations = $workflowSteps
            ->keyBy(fn (WorkflowStep $step): int => (int) $step->step_order)
            ->map(fn (WorkflowStep $step): ?int => $step->organization_id !== null ? (int) $step->organization_id : null);

        $assignments = [
            'analista.processos@example.com' => array_filter([
                $stepOrganizations->get(1),
                $stepOrganizations->get(3),
                $stepOrganizations->get(4),
            ]),
            'compras@example.com' => array_filter([
                $stepOrganizations->get(2),
            ]),
            'planejamento@example.com' => array_filter([
                $stepOrganizations->get(5),
            ]),
            'gestao.contratos@example.com' => array_filter([
                $stepOrganizations->get(6),
                $stepOrganizations->get(7),
                $stepOrganizations->get(9),
                $stepOrganizations->get(14),
            ]),
            'admin@example.com' => $workflowSteps
                ->pluck('organization_id')
                ->filter()
                ->map(fn ($id): int => (int) $id)
                ->unique()
                ->values()
                ->all(),
        ];

        foreach ($assignments as $email => $organizationIds) {
            if ($organizationIds === []) {
                continue;
            }

            $actors[$email]->organizations()->syncWithoutDetaching($organizationIds);
        }

        return $actors;
    }

    private function ensureUser(string $name, string $email): User
    {
        /** @var User $user */
        $user = User::query()->firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make('password'),
            ],
        );

        return $user;
    }

    /**
     * @param  array<string, \App\Models\Administration\User\User>  $actors
     * @param  array<string, mixed>  $scenario
     */
    private function seedScenario(
        Workflow $workflow,
        Collection $workflowSteps,
        array $actors,
        array $scenario,
    ): void {
        $createdAt = Carbon::parse($scenario['created_at']);
        $startedAt = isset($scenario['started_at']) ? Carbon::parse($scenario['started_at']) : null;
        $currentStepStartedAt = isset($scenario['current_step_started_at'])
            ? Carbon::parse($scenario['current_step_started_at'])
            : null;
        $finishedAt = isset($scenario['finished_at']) ? Carbon::parse($scenario['finished_at']) : null;
        $status = (string) $scenario['status'];
        $completedSteps = (int) ($scenario['completed_steps'] ?? 0);
        $currentStepOrder = $scenario['current_step_order'] ?? null;

        DB::transaction(function () use (
            $workflow,
            $workflowSteps,
            $actors,
            $scenario,
            $createdAt,
            $startedAt,
            $currentStepStartedAt,
            $finishedAt,
            $status,
            $completedSteps,
            $currentStepOrder,
        ): void {
            $process = Process::query()->create([
                'title' => $scenario['title'],
                'description' => $scenario['description'],
                'organization_id' => $this->resolveProcessOrganizationId(
                    $workflowSteps,
                    $status,
                    $completedSteps,
                    $currentStepOrder,
                    $scenario['organization_id'] ?? null,
                ),
                'workflow_id' => (bool) ($scenario['with_workflow'] ?? true) ? $workflow->id : null,
                'opened_by' => $actors[$scenario['opened_by']]->id,
                'owner_id' => $actors[$scenario['owner']]->id,
                'priority' => $scenario['priority'],
                'status' => $status,
                'started_at' => $startedAt,
                'closed_at' => $status === ProcessStatus::CLOSED ? $finishedAt : null,
            ]);

            if ((bool) ($scenario['with_workflow'] ?? true)) {
                $this->seedSteps(
                    $process,
                    $workflowSteps,
                    $createdAt,
                    $completedSteps,
                    $currentStepOrder,
                    $currentStepStartedAt,
                );

                $process->organizations()->sync(
                    $workflowSteps
                        ->pluck('organization_id')
                        ->filter()
                        ->map(fn ($id): int => (int) $id)
                        ->unique()
                        ->values()
                        ->all()
                );
            } elseif (! empty($scenario['organization_id'])) {
                $process->organizations()->sync([(int) $scenario['organization_id']]);
            }

            $eventNumber = 1;
            $lastEventAt = $this->seedEvents(
                $process,
                $workflowSteps,
                $actors,
                $scenario,
                $createdAt,
                $startedAt,
                $currentStepStartedAt,
                $finishedAt,
                $completedSteps,
                $eventNumber,
            );

            DB::table('processes')
                ->where('id', $process->id)
                ->update([
                    'created_at' => $createdAt,
                    'updated_at' => $lastEventAt ?? $finishedAt ?? $currentStepStartedAt ?? $startedAt ?? $createdAt,
                ]);
        });
    }

    private function resolveProcessOrganizationId(
        Collection $workflowSteps,
        string $status,
        int $completedSteps,
        int|string|null $currentStepOrder,
        int|string|null $fallbackOrganizationId,
    ): ?int {
        if ($fallbackOrganizationId !== null) {
            return (int) $fallbackOrganizationId;
        }

        if ($status === ProcessStatus::IN_PROGRESS && $currentStepOrder !== null) {
            return $workflowSteps
                ->firstWhere('step_order', (int) $currentStepOrder)
                ?->organization_id;
        }

        if ($status === ProcessStatus::CLOSED) {
            return $workflowSteps
                ->reverse()
                ->first(fn (WorkflowStep $step): bool => $step->organization_id !== null)
                ?->organization_id;
        }

        if ($completedSteps > 0) {
            return $workflowSteps
                ->where('step_order', '<=', $completedSteps)
                ->reverse()
                ->first(fn (WorkflowStep $step): bool => $step->organization_id !== null)
                ?->organization_id;
        }

        return $workflowSteps->first(fn (WorkflowStep $step): bool => $step->organization_id !== null)?->organization_id;
    }

    private function seedSteps(
        Process $process,
        Collection $workflowSteps,
        Carbon $createdAt,
        int $completedSteps,
        int|string|null $currentStepOrder,
        ?Carbon $currentStepStartedAt,
    ): void {
        $cursor = $createdAt->copy()->addHours(6);

        foreach ($workflowSteps as $workflowStep) {
            $stepOrder = (int) $workflowStep->step_order;
            $status = 'PENDING';
            $isCurrent = false;
            $startedAt = null;
            $completedAt = null;

            if ($stepOrder <= $completedSteps) {
                $startedAt = $cursor->copy();
                $completedAt = $startedAt->copy()->addDays($this->resolveCompletedStepDuration($workflowStep, $stepOrder));
                $status = 'COMPLETED';
                $cursor = $completedAt->copy()->addDay();
            } elseif ($currentStepOrder !== null && $stepOrder === (int) $currentStepOrder) {
                $startedAt = $currentStepStartedAt?->copy() ?? $cursor->copy();
                $status = 'IN_PROGRESS';
                $isCurrent = true;
            }

            $step = ProcessStep::query()->create([
                'process_id' => $process->id,
                'step_order' => $stepOrder,
                'title' => $workflowStep->title,
                'organization_id' => $workflowStep->organization_id,
                'deadline_days' => $workflowStep->deadline_days,
                'required' => (bool) $workflowStep->required,
                'is_current' => $isCurrent,
                'status' => $status,
                'started_at' => $startedAt,
                'completed_at' => $completedAt,
            ]);

            DB::table('process_steps')
                ->where('id', $step->id)
                ->update([
                    'created_at' => $startedAt ?? $createdAt,
                    'updated_at' => $completedAt ?? $startedAt ?? $createdAt,
                ]);
        }
    }

    private function resolveCompletedStepDuration(WorkflowStep $workflowStep, int $stepOrder): int
    {
        $deadline = max(1, (int) ($workflowStep->deadline_days ?? 1));
        $multiplier = $stepOrder % 2 === 0 ? 0.35 : 0.45;

        return max(1, (int) ceil($deadline * $multiplier));
    }

    /**
     * @param  array<string, \App\Models\Administration\User\User>  $actors
     * @param  array<string, mixed>  $scenario
     */
    private function seedEvents(
        Process $process,
        Collection $workflowSteps,
        array $actors,
        array $scenario,
        Carbon $createdAt,
        ?Carbon $startedAt,
        ?Carbon $currentStepStartedAt,
        ?Carbon $finishedAt,
        int $completedSteps,
        int &$eventNumber,
    ): ?Carbon {
        $lastEventAt = $createdAt->copy();
        $openedBy = $actors[$scenario['opened_by']];
        $owner = $actors[$scenario['owner']];

        $lastEventAt = $this->createEvent(
            $process,
            $eventNumber++,
            ProcessEventType::CREATED->value,
            'Processo cadastrado para acompanhamento do fluxo administrativo.',
            $openedBy->id,
            $createdAt,
        );

        if ($owner->id !== $openedBy->id) {
            $lastEventAt = $this->createEvent(
                $process,
                $eventNumber++,
                ProcessEventType::OWNER_ASSIGNED->value,
                'Responsável definido para condução do processo: '.$owner->name.'.',
                $openedBy->id,
                $createdAt->copy()->addMinutes(30),
            );
        }

        if ($startedAt !== null) {
            $lastEventAt = $this->createEvent(
                $process,
                $eventNumber++,
                ProcessEventType::STARTED->value,
                'Fluxo iniciado com vínculo ao workflow licitatório.',
                $owner->id,
                $startedAt,
            );
        }

        for ($order = 1; $order <= $completedSteps; $order++) {
            /** @var WorkflowStep|null $step */
            $step = $workflowSteps->firstWhere('step_order', $order);
            /** @var ProcessStep|null $processStep */
            $processStep = $process->steps()->where('step_order', $order)->first();

            if (! $step || ! $processStep?->completed_at) {
                continue;
            }

            $lastEventAt = $this->createEvent(
                $process,
                $eventNumber++,
                ProcessEventType::FORWARDED->value,
                'Etapa concluída e encaminhada: '.$step->title.'.',
                $owner->id,
                $processStep->completed_at,
            );
        }

        if (! empty($scenario['current_step_order']) && $currentStepStartedAt !== null) {
            /** @var WorkflowStep|null $currentStep */
            $currentStep = $workflowSteps->firstWhere('step_order', (int) $scenario['current_step_order']);

            if ($currentStep) {
                $commentedAt = $currentStepStartedAt->copy()->addHours(6);
                if ($commentedAt->gt(now()->subHour())) {
                    $commentedAt = now()->subHour();
                }

                $lastEventAt = $this->createEvent(
                    $process,
                    $eventNumber++,
                    ProcessEventType::COMMENTED->value,
                    'Despacho registrado na etapa atual: '.$currentStep->title.'.',
                    $owner->id,
                    $commentedAt,
                );
            }
        }

        if (($scenario['status'] ?? null) === ProcessStatus::CLOSED && $finishedAt !== null) {
            $lastEventAt = $this->createEvent(
                $process,
                $eventNumber++,
                ProcessEventType::CLOSED->value,
                'Processo encerrado após conclusão integral do workflow.',
                $owner->id,
                $finishedAt,
            );
        }

        if (($scenario['status'] ?? null) === ProcessStatus::CANCELLED && $finishedAt !== null) {
            $lastEventAt = $this->createEvent(
                $process,
                $eventNumber++,
                ProcessEventType::CANCELLED->value,
                'Processo cancelado por mudança de prioridade administrativa.',
                $owner->id,
                $finishedAt,
            );
        }

        return $lastEventAt;
    }

    private function createEvent(
        Process $process,
        int $eventNumber,
        string $eventType,
        string $description,
        int $userId,
        Carbon $createdAt,
    ): Carbon {
        DB::table('process_events')->insert([
            'uuid' => (string) Str::uuid(),
            'process_id' => $process->id,
            'event_number' => $eventNumber,
            'event_type' => $eventType,
            'description' => $description,
            'user_id' => $userId,
            'created_at' => $createdAt,
        ]);

        return $createdAt;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function scenarios(int $workflowStepsCount): array
    {
        $maxStepOrder = max(1, $workflowStepsCount);
        $maxCompletedInProgress = max(0, $maxStepOrder - 1);

        $openedByPool = [
            'admin@example.com',
            'analista.processos@example.com',
            'test@example.com',
            'planejamento@example.com',
        ];

        $ownerPool = [
            'gestao.contratos@example.com',
            'compras@example.com',
            'analista.processos@example.com',
            'planejamento@example.com',
        ];

        $priorities = ['normal', 'high', 'urgent'];

        $titles = [
            'Aquisição de insumos laboratoriais',
            'Contratação de manutenção predial',
            'Registro de preços de medicamentos',
            'Aquisição de equipamentos de informática',
            'Contratação de software de gestão',
            'Aquisição de material de expediente',
            'Contratação de serviços terceirizados',
            'Aquisição de mobiliário administrativo',
        ];

        $scenarios = [];
        $globalIndex = 1;

        for ($monthOffset = self::MONTHS_BACK - 1; $monthOffset >= 0; $monthOffset--) {
            $monthStart = now()->subMonths($monthOffset)->startOfMonth();
            $monthlyCount = $this->resolveMonthlyVolume($monthStart, $monthOffset);

            for ($monthItem = 1; $monthItem <= $monthlyCount; $monthItem++) {
                $status = $this->resolveStatusForIndex($globalIndex);
                $createdAt = $this->resolveCreatedAtWithinMonth($monthStart, $monthlyCount, $monthItem);
                $startedAt = $createdAt->copy()->addHours(($globalIndex % 5) + 1);

            $scenario = [
                'title' => sprintf(
                    '%s %s #%04d',
                    $titles[$globalIndex % count($titles)],
                    $monthStart->format('m/Y'),
                    $globalIndex
                ),
                'description' => 'Processo gerado para massa de dados com rastreabilidade completa de etapas e eventos.',
                'opened_by' => $openedByPool[$globalIndex % count($openedByPool)],
                'owner' => $ownerPool[$globalIndex % count($ownerPool)],
                'priority' => $priorities[$globalIndex % count($priorities)],
                'status' => $status,
                'created_at' => $createdAt->toDateTimeString(),
                'started_at' => $startedAt->toDateTimeString(),
                'completed_steps' => 0,
                'current_step_order' => 1,
            ];

            if ($status === ProcessStatus::CLOSED) {
                $finishedAt = $startedAt->copy()->addDays(($globalIndex % 30) + 3);

                $scenario['finished_at'] = $finishedAt->toDateTimeString();
                $scenario['completed_steps'] = $maxStepOrder;
                $scenario['current_step_order'] = null;
            }

            if ($status === ProcessStatus::IN_PROGRESS) {
                $completedSteps = $maxCompletedInProgress > 0 ? ($globalIndex % ($maxCompletedInProgress + 1)) : 0;
                $currentStepOrder = min($completedSteps + 1, $maxStepOrder);
                $currentStepStartedAt = $startedAt->copy()->addDays(max(0, $completedSteps));

                if ($currentStepStartedAt->gt(now()->subHour())) {
                    $currentStepStartedAt = now()->subHour();
                }

                $scenario['completed_steps'] = $completedSteps;
                $scenario['current_step_order'] = $currentStepOrder;
                $scenario['current_step_started_at'] = $currentStepStartedAt->toDateTimeString();
            }

            if ($status === ProcessStatus::CANCELLED) {
                $completedSteps = min(max(1, $globalIndex % max(2, $maxCompletedInProgress + 1)), $maxCompletedInProgress);
                $finishedAt = $startedAt->copy()->addDays(max(2, $completedSteps + 1));

                $scenario['completed_steps'] = $completedSteps;
                $scenario['current_step_order'] = null;
                $scenario['finished_at'] = $finishedAt->toDateTimeString();
            }

            $scenarios[] = $scenario;
                $globalIndex++;
            }
        }

        return $scenarios;
    }

    private function resolveMonthlyVolume(Carbon $monthStart, int $monthOffset): int
    {
        $range = self::MAX_PROCESSES_PER_MONTH - self::MIN_PROCESSES_PER_MONTH;
        $seed = (($monthOffset + 1) * 37) + ((int) $monthStart->month * 11) + ((int) $monthStart->year % 100);

        return self::MIN_PROCESSES_PER_MONTH + ($seed % ($range + 1));
    }

    private function resolveCreatedAtWithinMonth(Carbon $monthStart, int $monthlyCount, int $position): Carbon
    {
        $monthEnd = $monthStart->copy()->endOfMonth()->endOfDay();
        $windowEnd = $monthEnd->lt(now()) ? $monthEnd : now()->subMinutes(5);
        $windowSeconds = max(1, $monthStart->diffInSeconds($windowEnd));
        $slotSeconds = (int) floor(($position * $windowSeconds) / ($monthlyCount + 1));

        return $monthStart->copy()->addSeconds($slotSeconds);
    }

    private function resolveStatusForIndex(int $index): string
    {
        $distribution = $index % 100;

        if ($distribution < 62) {
            return ProcessStatus::CLOSED;
        }

        if ($distribution < 92) {
            return ProcessStatus::IN_PROGRESS;
        }

        return ProcessStatus::CANCELLED;
    }
}
