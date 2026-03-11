<?php

namespace App\Livewire\Process;

use App\Enums\Process\ProcessStatus;
use App\Livewire\Traits\WithFlashMessage;
use App\Models\Process\Process;
use App\Services\Process\ProcessService;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Throwable;

#[Layout('layouts.app')]
class ProcessShowPage extends Component
{
    use WithFlashMessage;

    protected ProcessService $processService;

    public string $uuid;

    public function boot(ProcessService $processService): void
    {
        $this->processService = $processService;
    }

    public function mount(string $uuid): void
    {
        $this->uuid = $uuid;
        $this->authorize('process.view');
    }

    public function advanceStep(): void
    {
        $this->authorize('process.view');

        try {
            $process = $this->processService->findByUuid($this->uuid);
            $this->processService->advanceStep($process);
            $this->flashSuccess('Etapa avancada com sucesso.');
        } catch (InvalidArgumentException $e) {
            $this->flashWarning($e->getMessage());
        } catch (Throwable) {
            $this->flashError('Nao foi possivel avancar a etapa.');
        }
    }

    public function render()
    {
        $process = $this->processService->findByUuid($this->uuid);
        $timelineSteps = $this->buildTimelineSteps($process);

        return view('livewire.process.process-show-page', [
            'process' => $process,
            'timelineSteps' => $timelineSteps,
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
