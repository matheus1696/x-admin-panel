<?php

namespace App\Http\Controllers\Process;

use App\Http\Controllers\Controller;
use App\Services\Process\ProcessService;
use Illuminate\Http\RedirectResponse;
use InvalidArgumentException;
use Throwable;

class ProcessFlowController extends Controller
{
    public function __construct(
        private readonly ProcessService $processService,
    ) {
    }

    public function advance(string $uuid): RedirectResponse
    {
        try {
            $process = $this->processService->findByUuid($uuid);
            $this->processService->advanceStep($process);

            return redirect()
                ->route('process.show', $uuid)
                ->with('success', 'Etapa avancada com sucesso.');
        } catch (InvalidArgumentException $e) {
            return redirect()
                ->route('process.show', $uuid)
                ->with('warning', $e->getMessage());
        } catch (Throwable) {
            return redirect()
                ->route('process.show', $uuid)
                ->with('error', 'Nao foi possivel avancar a etapa.');
        }
    }
}
