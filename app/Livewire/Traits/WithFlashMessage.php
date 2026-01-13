<?php

namespace App\Livewire\Traits;

trait WithFlashMessage
{
    protected function flash(
        string $type,
        string $message
    ): void {
        session()->flash($type, $message);

        // Evento para Alpine/JS reanimar
        $this->dispatch('flash-show');
    }

    protected function flashSuccess(string $message): void
    {
        $this->flash('success', $message);
    }

    protected function flashError(string $message): void
    {
        $this->flash('error', $message);
    }

    protected function flashWarning(string $message): void
    {
        $this->flash('warning', $message);
    }

    protected function flashInfo(string $message): void
    {
        $this->flash('info', $message);
    }
}
