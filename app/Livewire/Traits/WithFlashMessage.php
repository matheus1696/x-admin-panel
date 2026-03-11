<?php

namespace App\Livewire\Traits;

trait WithFlashMessage
{
    protected function flash(string $type, string $message): void
    {
        session()->flash($type, $message);

        // Evento global para toasts em ações Livewire
        $this->dispatch('app-flash', type: $type, message: $message);
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
