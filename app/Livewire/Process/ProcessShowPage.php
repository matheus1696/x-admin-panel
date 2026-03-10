<?php

namespace App\Livewire\Process;

use App\Enums\Process\ProcessStatus;
use App\Livewire\Traits\WithFlashMessage;
use App\Models\Process\Process;
use App\Services\Process\ProcessService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ProcessShowPage extends Component
{
    use AuthorizesRequests;
    use WithFlashMessage;

    protected ProcessService $processService;

    public string $uuid;
    public ?string $cancelNote = null;

    public function boot(ProcessService $processService): void
    {
        $this->processService = $processService;
    }

    public function mount(string $uuid): void
    {
        $this->uuid = $uuid;
        $this->authorize('process.view');
    }

    public function start(): void
    {
        $process = $this->processService->findByUuid($this->uuid);
        $this->authorize('process.manage');

        try {
            $this->processService->start($process, (int) Auth::id());
            $this->flashSuccess('Processo iniciado com sucesso.');
        } catch (InvalidArgumentException $exception) {
            $this->flashError($exception->getMessage());
        }
    }

    public function close(): void
    {
        $process = $this->processService->findByUuid($this->uuid);

        try {
            $this->processService->close($process, (int) Auth::id());
            $this->flashSuccess('Processo encerrado com sucesso.');
        } catch (InvalidArgumentException $exception) {
            $this->flashError($exception->getMessage());
        }
    }

    public function cancel(): void
    {
        $this->validate([
            'cancelNote' => ['required', 'string', 'max:2000'],
        ]);

        $process = $this->processService->findByUuid($this->uuid);
        $this->authorize('process.manage');

        try {
            $this->processService->cancel($process, (int) Auth::id(), (string) $this->cancelNote);
            $this->flashSuccess('Processo cancelado com sucesso.');
            $this->cancelNote = null;
        } catch (InvalidArgumentException $exception) {
            $this->flashError($exception->getMessage());
        }
    }

    public function advanceStep(): void
    {
        $this->handleStepTransition('advance');
    }

    public function retreatStep(): void
    {
        $this->handleStepTransition('retreat');
    }

    private function handleStepTransition(string $action): void
    {
        $process = $this->processService->findByUuid($this->uuid);
        $this->authorize('process.manage');

        try {
            if ($action === 'advance') {
                $this->processService->advanceStep($process);
                $this->flashSuccess('Etapa atual concluida e proxima iniciada com sucesso.');
                return;
            }

            if ($action === 'retreat') {
                $this->processService->retreatStep($process);
                $this->flashSuccess('Etapa atual retornada e etapa anterior iniciada com sucesso.');
                return;
            }

            $this->flashError('Acao de etapa invalida.');
        } catch (InvalidArgumentException $exception) {
            $this->flashError($exception->getMessage());
        }
    }

    public function render()
    {
        $process = $this->processService->findByUuid($this->uuid);
        $timelineSteps = $this->buildTimelineSteps($process);

        return view('livewire.process.process-show-page', [
            'process' => $process,
            'timelineSteps' => $timelineSteps,
            'canAdvanceStep' => $this->canAdvanceStep($process),
            'canRetreatStep' => $this->canRetreatStep($process),
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
        if ($process->status === ProcessStatus::CLOSED->value) {
            return 'Concluida';
        }

        if ((bool) ($step->is_current ?? false) === true) {
            return 'Em andamento';
        }

        if ($step->completed_at !== null) {
            return 'Concluida';
        }

        return 'Pendente';
    }

    private function canAdvanceStep(Process $process): bool
    {
        if (in_array($process->status, [ProcessStatus::CLOSED->value, ProcessStatus::CANCELLED->value], true)) {
            return false;
        }

        $steps = ($process->steps ?? collect())->values();
        if ($steps->count() < 2) {
            return false;
        }

        $currentIndex = $this->resolveCurrentStepIndex($steps);
        if ($currentIndex === false) {
            return false;
        }

        return $steps->get((int) $currentIndex + 1) !== null;
    }

    private function canRetreatStep(Process $process): bool
    {
        if (in_array($process->status, [ProcessStatus::CLOSED->value, ProcessStatus::CANCELLED->value], true)) {
            return false;
        }

        $steps = ($process->steps ?? collect())->values();
        if ($steps->count() < 2) {
            return false;
        }

        $currentIndex = $this->resolveCurrentStepIndex($steps);
        if ($currentIndex === false) {
            return false;
        }

        return $steps->get((int) $currentIndex - 1) !== null;
    }

    private function resolveCurrentStepIndex(Collection $steps): int|false
    {
        $currentStep = $steps->first(fn (object $step): bool => (bool) ($step->is_current ?? false) === true);
        if (! $currentStep) {
            $currentStep = $steps->first(fn (object $step): bool => $step->completed_at === null);
        }

        if (! $currentStep) {
            $currentStep = $steps->last();
        }

        if (! $currentStep) {
            return false;
        }

        return $steps->search(fn (object $step): bool => (int) $step->id === (int) $currentStep->id);
    }
}
