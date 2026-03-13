<?php

namespace App\Livewire\Process;

use App\Livewire\Traits\Modal;
use App\Livewire\Traits\WithFlashMessage;
use App\Models\Administration\User\User;
use App\Models\Process\Process;
use App\Models\Process\ProcessStatus;
use App\Services\Process\ProcessService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Throwable;

#[Layout('layouts.app')]
class ProcessShowPage extends Component
{
    use Modal;
    use WithFlashMessage;

    protected ProcessService $processService;

    public string $uuid;

    public string $dispatchComment = '';

    public string $commentText = '';

    public ?int $assignedOwnerId = null;

    public ?string $pendingTransition = null;

    public function boot(ProcessService $processService): void
    {
        $this->processService = $processService;
    }

    public function mount(string $uuid): void
    {
        $this->uuid = $uuid;

        if (! Auth::check()) {
            $this->redirectRoute('dashboard', navigate: true);

            return;
        }

        $process = $this->processService->findByUuid($uuid);
        if (! $this->processService->userCanView($process, (int) Auth::id())) {
            $this->redirectRoute('dashboard', navigate: true);

            return;
        }

        $this->processService->markAsViewed($process, (int) Auth::id());
    }

    public function openDispatchModal(string $direction): void
    {
        $process = $this->processService->findVisibleByUuid($this->uuid, (int) Auth::id());

        if (! $this->processService->userCanManageCurrentStepActions($process, (int) Auth::id())) {
            $this->flashWarning('Somente usuario do setor da etapa atual pode executar esta acao.');

            return;
        }

        if (! in_array($direction, ['advance', 'retreat'], true)) {
            $this->flashWarning('Acao de transicao invalida.');

            return;
        }

        $this->pendingTransition = $direction;
        $this->dispatchComment = '';

        $this->assignedOwnerId = null;

        $this->openModal('modal-process-dispatch');
    }

    public function confirmStepTransition(): void
    {
        $process = $this->processService->findVisibleByUuid($this->uuid, (int) Auth::id());
        $requiresOwnerBeforeAdvance = $this->pendingTransition === 'advance' && $process->owner_id === null;

        if (! in_array($this->pendingTransition, ['advance', 'retreat'], true)) {
            $this->flashWarning('Acao de transicao invalida.');
            $this->closeModal();

            return;
        }

        $this->validate([
            'dispatchComment' => ['required', 'string', 'max:2000'],
            'assignedOwnerId' => $requiresOwnerBeforeAdvance ? ['required', 'integer', 'exists:users,id'] : ['nullable'],
        ]);

        try {
            if ($requiresOwnerBeforeAdvance) {
                $this->processService->assignOwner(
                    $process,
                    (int) Auth::id(),
                    (int) $this->assignedOwnerId
                );

                $process = $process->refresh();
            }

            if ($this->pendingTransition === 'advance') {
                $this->processService->advanceStep($process, (int) Auth::id(), $this->dispatchComment);
            } else {
                $this->processService->retreatStep($process, (int) Auth::id(), $this->dispatchComment);
            }

            $successMessage = $this->pendingTransition === 'advance'
                ? 'Etapa avancada com sucesso.'
                : 'Etapa retrocedida com sucesso.';

            $this->dispatchComment = '';
            $this->assignedOwnerId = null;
            $this->pendingTransition = null;
            $this->closeModal();
            $this->flashSuccess($successMessage);
        } catch (InvalidArgumentException $e) {
            $this->flashWarning($e->getMessage());
        } catch (Throwable) {
            $this->flashError('Nao foi possivel concluir a transicao de etapa.');
        }
    }

    public function openCommentModal(): void
    {
        $this->commentText = '';
        $this->openModal('modal-process-comment');
    }

    public function saveComment(): void
    {
        $process = $this->processService->findVisibleByUuid($this->uuid, (int) Auth::id());
        $this->validate([
            'commentText' => ['required', 'string', 'max:2000'],
        ]);

        try {
            $this->processService->comment($process, (int) Auth::id(), $this->commentText);
            $this->commentText = '';
            $this->closeModal();
            $this->flashSuccess('Comentario registrado como despacho.');
        } catch (InvalidArgumentException $e) {
            $this->flashWarning($e->getMessage());
        } catch (Throwable) {
            $this->flashError('Nao foi possivel registrar o comentario.');
        }
    }

    public function openAssignOwnerModal(): void
    {
        $process = $this->processService->findVisibleByUuid($this->uuid, (int) Auth::id());

        if (! $this->processService->userCanManageCurrentStepActions($process, (int) Auth::id())) {
            $this->flashWarning('Somente usuario do setor da etapa atual pode executar esta acao.');

            return;
        }

        $eligibleOwners = $this->eligibleOwnersForCurrentStep($process);
        $eligibleOwnerIds = $eligibleOwners->pluck('id');

        $this->assignedOwnerId = $eligibleOwnerIds->contains((int) $process->owner_id)
            ? (int) $process->owner_id
            : ($eligibleOwnerIds->first() ? (int) $eligibleOwnerIds->first() : null);

        $this->openModal('modal-process-assign-owner');
    }

    public function assignOwner(): void
    {
        $process = $this->processService->findVisibleByUuid($this->uuid, (int) Auth::id());
        $this->validate([
            'assignedOwnerId' => ['required', 'integer', 'exists:users,id'],
        ]);

        try {
            $this->processService->assignOwner(
                $process,
                (int) Auth::id(),
                (int) $this->assignedOwnerId
            );
            $this->closeModal();
            $this->flashSuccess('Responsavel atribuido com sucesso.');
        } catch (InvalidArgumentException $e) {
            $this->flashWarning($e->getMessage());
        } catch (Throwable) {
            $this->flashError('Nao foi possivel atribuir o responsavel.');
        }
    }

    public function render()
    {
        $process = $this->processService->findVisibleByUuid($this->uuid, (int) Auth::id());
        $canManageStepActions = $this->processService
            ->userCanManageCurrentStepActions($process, (int) Auth::id());
        $timelineSteps = $this->buildTimelineSteps($process);
        $owners = $this->eligibleOwnersForCurrentStep($process);

        return view('livewire.process.process-show-page', [
            'process' => $process,
            'timelineSteps' => $timelineSteps,
            'owners' => $owners,
            'canManageStepActions' => $canManageStepActions,
            'requiresOwnerBeforeAdvance' => $process->owner_id === null,
        ]);
    }

    private function eligibleOwnersForCurrentStep(Process $process): Collection
    {
        $currentStepOrganizationId = $this->processService->resolveCurrentStepOrganizationId($process);

        if ($currentStepOrganizationId === null) {
            return collect();
        }

        return User::query()
            ->whereHas('organizations', fn ($query) => $query->where('organization_charts.id', $currentStepOrganizationId))
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    private function buildTimelineSteps(Process $process): Collection
    {
        $steps = ($process->steps ?? collect())->values();

        if ($steps->isEmpty()) {
            return collect();
        }

        return $steps->map(function ($step) use ($process) {
            $state = $this->resolveStepStateLabel($process, $step);
            $isOverdue = $state === 'Em andamento' && $this->isStepOverdue($step);
            $completedDelayDays = $this->resolveCompletedDelayDays($step, $state);

            return [
                'id' => (int) $step->id,
                'title' => (string) $step->title,
                'required' => (bool) $step->required,
                'deadline_days' => $step->deadline_days,
                'organization_title' => $step->organization?->title,
                'owner_name' => match ($state) {
                    'Pendente' => '-',
                    default => $step->owner?->name ?? 'Nao atribuido',
                },
                'started_at' => $step->started_at,
                'state' => $state,
                'is_overdue' => $isOverdue,
                'completed_with_delay' => $completedDelayDays > 0,
                'completed_delay_days' => $completedDelayDays,
            ];
        });
    }

    private function isStepOverdue(object $step): bool
    {
        if ($step->started_at === null || $step->deadline_days === null) {
            return false;
        }

        $dueAt = $step->started_at->copy()->addDays((int) $step->deadline_days);

        return $dueAt->lt(now());
    }

    private function resolveCompletedDelayDays(object $step, string $state): int
    {
        if ($state !== 'Concluida' || $step->started_at === null || $step->completed_at === null || $step->deadline_days === null) {
            return 0;
        }

        $dueAt = $step->started_at->copy()->addDays((int) $step->deadline_days);

        if (! $step->completed_at->gt($dueAt)) {
            return 0;
        }

        return (int) ceil($dueAt->diffInDays($step->completed_at, true));
    }

    private function resolveStepStateLabel(Process $process, object $step): string
    {
        $status = strtoupper((string) ($step->status ?? ''));

        if ((bool) ($step->is_current ?? false) === true || $status === 'IN_PROGRESS') {
            return 'Em andamento';
        }

        if ($status === 'COMPLETED') {
            return 'Concluida';
        }

        if ($status === 'PENDING') {
            return 'Pendente';
        }

        if ($process->status === ProcessStatus::CLOSED) {
            return 'Concluida';
        }

        if ($step->completed_at !== null) {
            return 'Concluida';
        }

        return 'Pendente';
    }
}
