<?php

namespace App\Livewire\Process;

use App\Enums\Process\ProcessStatus;
use App\Livewire\Traits\Modal;
use App\Livewire\Traits\WithFlashMessage;
use App\Models\Administration\User\User;
use App\Models\Process\Process;
use App\Services\Process\ProcessService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
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
    public string $assignmentComment = '';
    public ?string $pendingTransition = null;

    public function boot(ProcessService $processService): void
    {
        $this->processService = $processService;
    }

    public function mount(string $uuid): void
    {
        $this->uuid = $uuid;
        $this->authorize('process.view');
    }

    public function openDispatchModal(string $direction): void
    {
        $this->authorize('process.view');
        $process = $this->processService->findByUuid($this->uuid);

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
        $this->openModal('modal-process-dispatch');
    }

    public function confirmStepTransition(): void
    {
        $this->authorize('process.view');

        if (! in_array($this->pendingTransition, ['advance', 'retreat'], true)) {
            $this->flashWarning('Acao de transicao invalida.');
            $this->closeModal();
            return;
        }

        $this->validate([
            'dispatchComment' => ['required', 'string', 'max:2000'],
        ]);

        try {
            $process = $this->processService->findByUuid($this->uuid);
            if ($this->pendingTransition === 'advance') {
                $this->processService->advanceStep($process, (int) Auth::id(), $this->dispatchComment);
            } else {
                $this->processService->retreatStep($process, (int) Auth::id(), $this->dispatchComment);
            }

            $successMessage = $this->pendingTransition === 'advance'
                ? 'Etapa avancada com sucesso.'
                : 'Etapa retrocedida com sucesso.';

            $this->dispatchComment = '';
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
        $this->authorize('process.view');
        $this->commentText = '';
        $this->openModal('modal-process-comment');
    }

    public function saveComment(): void
    {
        $this->authorize('process.view');
        $this->validate([
            'commentText' => ['required', 'string', 'max:2000'],
        ]);

        try {
            $process = $this->processService->findByUuid($this->uuid);
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
        $this->authorize('process.view');
        $process = $this->processService->findByUuid($this->uuid);

        if (! $this->processService->userCanManageCurrentStepActions($process, (int) Auth::id())) {
            $this->flashWarning('Somente usuario do setor da etapa atual pode executar esta acao.');
            return;
        }

        $currentStepOrganizationId = $this->processService->resolveCurrentStepOrganizationId($process);

        $eligibleOwnerIds = $currentStepOrganizationId === null
            ? collect()
            : User::query()
                ->whereHas('organizations', fn ($query) => $query->where('organization_charts.id', $currentStepOrganizationId))
                ->pluck('id');

        $this->assignedOwnerId = $eligibleOwnerIds->contains((int) $process->owner_id)
            ? (int) $process->owner_id
            : ($eligibleOwnerIds->first() ? (int) $eligibleOwnerIds->first() : null);

        $this->assignmentComment = '';
        $this->openModal('modal-process-assign-owner');
    }

    public function assignOwner(): void
    {
        $this->authorize('process.view');
        $this->validate([
            'assignedOwnerId' => ['required', 'integer', 'exists:users,id'],
            'assignmentComment' => ['required', 'string', 'max:2000'],
        ]);

        try {
            $process = $this->processService->findByUuid($this->uuid);
            $this->processService->assignOwner(
                $process,
                (int) Auth::id(),
                (int) $this->assignedOwnerId,
                $this->assignmentComment
            );
            $this->assignmentComment = '';
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
        $process = $this->processService->findByUuid($this->uuid);
        $canManageStepActions = $this->processService
            ->userCanManageCurrentStepActions($process, (int) Auth::id());
        $currentStepOrganizationId = $this->processService->resolveCurrentStepOrganizationId($process);
        $timelineSteps = $this->buildTimelineSteps($process);

        $owners = $currentStepOrganizationId === null
            ? collect()
            : User::query()
                ->whereHas('organizations', fn ($query) => $query->where('organization_charts.id', $currentStepOrganizationId))
                ->orderBy('name')
                ->get(['id', 'name']);

        return view('livewire.process.process-show-page', [
            'process' => $process,
            'timelineSteps' => $timelineSteps,
            'owners' => $owners,
            'canManageStepActions' => $canManageStepActions,
        ]);
    }

    private function buildTimelineSteps(Process $process): Collection
    {
        $steps = ($process->steps ?? collect())->values();

        if ($steps->isEmpty()) {
            return collect();
        }

        return $steps->map(function ($step) use ($process) {
            $state = $this->resolveStepStateLabel($process, $step);

            return [
                'id' => (int) $step->id,
                'title' => (string) $step->title,
                'required' => (bool) $step->required,
                'deadline_days' => $step->deadline_days,
                'organization_title' => $step->organization?->title,
                'state' => $state,
            ];
        });
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

        if ($process->status === ProcessStatus::CLOSED->value) {
            return 'Concluida';
        }

        if ($step->completed_at !== null) {
            return 'Concluida';
        }

        return 'Pendente';
    }
}
