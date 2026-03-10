<?php

namespace App\Livewire\TimeClock;

use App\Services\TimeClock\TimeClockEntryService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class EntryShow extends Component
{
    use AuthorizesRequests;

    protected TimeClockEntryService $entryService;

    public string $uuid;

    public function boot(TimeClockEntryService $entryService): void
    {
        $this->entryService = $entryService;
    }

    public function mount(string $uuid): void
    {
        $this->uuid = $uuid;
        $entry = $this->entryService->findByUuid($uuid);
        $this->authorize('view', $entry);
    }

    public function render(): View
    {
        return view('livewire.time-clock.entry-show', [
            'entry' => $this->entryService->findByUuid($this->uuid),
        ]);
    }
}
