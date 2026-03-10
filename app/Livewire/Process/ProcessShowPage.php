<?php

namespace App\Livewire\Process;

use App\Livewire\Traits\WithFlashMessage;
use App\Models\Process\Process;
use App\Services\Process\ProcessService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
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
    public string $commentDraft = '';

    public function boot(ProcessService $processService): void
    {
        $this->processService = $processService;
    }

    public function mount(string $uuid): void
    {
        $this->uuid = $uuid;
        $process = $this->processService->findByUuid($uuid);
        $this->authorize('view', $process);
    }

    public function start(): void
    {
        $process = $this->processService->findByUuid($this->uuid);
        $this->authorize('manage', Process::class);

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
        $this->authorize('close', $process);

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
        $this->authorize('manage', Process::class);

        try {
            $this->processService->cancel($process, (int) Auth::id(), (string) $this->cancelNote);
            $this->flashSuccess('Processo cancelado com sucesso.');
            $this->cancelNote = null;
        } catch (InvalidArgumentException $exception) {
            $this->flashError($exception->getMessage());
        }
    }

    public function render()
    {
        $process = $this->processService->findByUuid($this->uuid);

        return view('livewire.process.process-show-page', [
            'process' => $process,
        ]);
    }
}
