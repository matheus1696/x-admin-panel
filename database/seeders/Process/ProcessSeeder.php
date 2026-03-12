<?php

namespace Database\Seeders\Process;

use App\Enums\Process\ProcessEventType;
use App\Enums\Process\ProcessStatus;
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
    public function run(): void
    {
        $workflow = Workflow::query()
            ->with(['workflowSteps' => fn ($query) => $query->orderBy('step_order')->orderBy('id')])
            ->first();

        if (! $workflow || $workflow->workflowSteps->isEmpty()) {
            return;
        }

        $actors = $this->ensureActors($workflow->workflowSteps);

        foreach ($this->scenarios() as $scenario) {
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
                'closed_at' => $status === ProcessStatus::CLOSED->value ? $finishedAt : null,
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

        if (in_array($status, [ProcessStatus::IN_PROGRESS->value, ProcessStatus::ON_HOLD->value], true) && $currentStepOrder !== null) {
            return $workflowSteps
                ->firstWhere('step_order', (int) $currentStepOrder)
                ?->organization_id;
        }

        if ($status === ProcessStatus::CLOSED->value) {
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

        if (($scenario['status'] ?? null) === ProcessStatus::CLOSED->value && $finishedAt !== null) {
            $lastEventAt = $this->createEvent(
                $process,
                $eventNumber++,
                ProcessEventType::CLOSED->value,
                'Processo encerrado após conclusão integral do workflow.',
                $owner->id,
                $finishedAt,
            );
        }

        if (($scenario['status'] ?? null) === ProcessStatus::CANCELLED->value && $finishedAt !== null) {
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
    private function scenarios(): array
    {
        return [
            [
                'title' => 'Aquisição de medicamentos da atenção básica 2025',
                'description' => 'Processo para recomposição de estoque da assistência farmacêutica com tramitação integral concluída.',
                'opened_by' => 'admin@example.com',
                'owner' => 'gestao.contratos@example.com',
                'priority' => 'high',
                'status' => ProcessStatus::CLOSED->value,
                'created_at' => '2025-10-03 08:30:00',
                'started_at' => '2025-10-03 10:00:00',
                'finished_at' => '2026-01-08 16:00:00',
                'completed_steps' => 16,
                'current_step_order' => null,
            ],
            [
                'title' => 'Contratação de manutenção preventiva dos consultórios odontológicos',
                'description' => 'Fluxo de contratação encerrado após publicação e emissão das ordens de serviço.',
                'opened_by' => 'analista.processos@example.com',
                'owner' => 'compras@example.com',
                'priority' => 'normal',
                'status' => ProcessStatus::CLOSED->value,
                'created_at' => '2025-10-21 09:00:00',
                'started_at' => '2025-10-21 11:00:00',
                'finished_at' => '2026-01-15 15:30:00',
                'completed_steps' => 16,
                'current_step_order' => null,
            ],
            [
                'title' => 'Registro de preços para materiais de limpeza hospitalar',
                'description' => 'Processo licitatório concluído para atendimento da rede especializada.',
                'opened_by' => 'test@example.com',
                'owner' => 'gestao.contratos@example.com',
                'priority' => 'high',
                'status' => ProcessStatus::CLOSED->value,
                'created_at' => '2025-11-11 14:20:00',
                'started_at' => '2025-11-12 08:00:00',
                'finished_at' => '2026-02-10 17:40:00',
                'completed_steps' => 16,
                'current_step_order' => null,
            ],
            [
                'title' => 'Aquisição de equipamentos de informática para unidades administrativas',
                'description' => 'Contratação encerrada com assinatura de atas e ordens de fornecimento emitidas.',
                'opened_by' => 'planejamento@example.com',
                'owner' => 'gestao.contratos@example.com',
                'priority' => 'normal',
                'status' => ProcessStatus::CLOSED->value,
                'created_at' => '2025-11-28 08:10:00',
                'started_at' => '2025-11-28 09:00:00',
                'finished_at' => '2026-02-25 18:00:00',
                'completed_steps' => 16,
                'current_step_order' => null,
            ],
            [
                'title' => 'Locação de veículos para apoio logístico da vigilância em saúde',
                'description' => 'Processo finalizado após homologação e publicação da fase externa.',
                'opened_by' => 'admin@example.com',
                'owner' => 'compras@example.com',
                'priority' => 'high',
                'status' => ProcessStatus::CLOSED->value,
                'created_at' => '2025-12-09 10:00:00',
                'started_at' => '2025-12-09 14:00:00',
                'finished_at' => '2026-03-05 12:30:00',
                'completed_steps' => 16,
                'current_step_order' => null,
            ],
            [
                'title' => 'Aquisição de insumos laboratoriais para a rede municipal',
                'description' => 'Processo em julgamento, aguardando parecer jurídico da fase externa.',
                'opened_by' => 'analista.processos@example.com',
                'owner' => 'gestao.contratos@example.com',
                'priority' => 'high',
                'status' => ProcessStatus::IN_PROGRESS->value,
                'created_at' => '2025-12-17 08:45:00',
                'started_at' => '2025-12-17 10:00:00',
                'current_step_order' => 11,
                'current_step_started_at' => '2026-03-08 09:00:00',
                'completed_steps' => 10,
            ],
            [
                'title' => 'Contratação de empresa para esterilização e manutenção de instrumental',
                'description' => 'Processo em execução com edital já publicado e sessão externa em andamento.',
                'opened_by' => 'test@example.com',
                'owner' => 'compras@example.com',
                'priority' => 'normal',
                'status' => ProcessStatus::IN_PROGRESS->value,
                'created_at' => '2026-01-06 08:10:00',
                'started_at' => '2026-01-06 09:00:00',
                'current_step_order' => 10,
                'current_step_started_at' => '2026-02-20 08:30:00',
                'completed_steps' => 9,
            ],
            [
                'title' => 'Registro de preços para aquisição de material de expediente',
                'description' => 'Processo em análise técnica após cotação consolidada.',
                'opened_by' => 'admin@example.com',
                'owner' => 'analista.processos@example.com',
                'priority' => 'normal',
                'status' => ProcessStatus::IN_PROGRESS->value,
                'created_at' => '2026-01-19 09:15:00',
                'started_at' => '2026-01-19 11:00:00',
                'current_step_order' => 3,
                'current_step_started_at' => '2026-03-10 09:00:00',
                'completed_steps' => 2,
            ],
            [
                'title' => 'Aquisição de mobiliário ergonômico para a sede administrativa',
                'description' => 'Fluxo aguardando autorização do ordenador de despesa.',
                'opened_by' => 'planejamento@example.com',
                'owner' => 'planejamento@example.com',
                'priority' => 'high',
                'status' => ProcessStatus::IN_PROGRESS->value,
                'created_at' => '2026-02-03 07:50:00',
                'started_at' => '2026-02-03 08:30:00',
                'current_step_order' => 5,
                'current_step_started_at' => '2026-03-11 08:00:00',
                'completed_steps' => 4,
            ],
            [
                'title' => 'Contratação de licenças de software para gestão ambulatorial',
                'description' => 'Processo em cadastro no sistema financeiro antes da elaboração do edital.',
                'opened_by' => 'admin@example.com',
                'owner' => 'gestao.contratos@example.com',
                'priority' => 'urgent',
                'status' => ProcessStatus::IN_PROGRESS->value,
                'created_at' => '2026-02-14 13:30:00',
                'started_at' => '2026-02-14 14:30:00',
                'current_step_order' => 6,
                'current_step_started_at' => '2026-03-11 14:00:00',
                'completed_steps' => 5,
            ],
            [
                'title' => 'Aquisição de impressoras térmicas para farmácias da rede',
                'description' => 'Processo em elaboração do estudo técnico com atraso frente ao prazo previsto.',
                'opened_by' => 'test@example.com',
                'owner' => 'analista.processos@example.com',
                'priority' => 'normal',
                'status' => ProcessStatus::ON_HOLD->value,
                'created_at' => '2026-02-27 09:40:00',
                'started_at' => '2026-02-27 10:15:00',
                'current_step_order' => 3,
                'current_step_started_at' => '2026-03-06 08:00:00',
                'completed_steps' => 2,
            ],
            [
                'title' => 'Registro de preços para gêneros alimentícios de apoio hospitalar',
                'description' => 'Processo em espera após parecer interno, aguardando reprogramação orçamentária.',
                'opened_by' => 'admin@example.com',
                'owner' => 'planejamento@example.com',
                'priority' => 'high',
                'status' => ProcessStatus::ON_HOLD->value,
                'created_at' => '2026-03-02 08:00:00',
                'started_at' => '2026-03-02 08:40:00',
                'current_step_order' => 9,
                'current_step_started_at' => '2026-03-05 09:00:00',
                'completed_steps' => 8,
            ],
            [
                'title' => 'Aquisição emergencial de nobreaks para unidades críticas',
                'description' => 'Processo aberto e aguardando definição do fluxo inicial de tramitação.',
                'opened_by' => 'admin@example.com',
                'owner' => 'analista.processos@example.com',
                'priority' => 'urgent',
                'status' => ProcessStatus::OPEN->value,
                'created_at' => '2026-03-07 07:30:00',
                'started_at' => null,
                'current_step_order' => null,
                'completed_steps' => 0,
            ],
            [
                'title' => 'Contratação de consultoria para revisão dos contratos assistenciais',
                'description' => 'Processo cancelado após redefinição do escopo e absorção interna da demanda.',
                'opened_by' => 'planejamento@example.com',
                'owner' => 'gestao.contratos@example.com',
                'priority' => 'normal',
                'status' => ProcessStatus::CANCELLED->value,
                'created_at' => '2026-01-28 10:20:00',
                'started_at' => '2026-01-28 11:00:00',
                'finished_at' => '2026-02-18 15:00:00',
                'current_step_order' => null,
                'completed_steps' => 4,
            ],
        ];
    }
}
