<?php

namespace App\Livewire\TimeClock;

use App\Models\TimeClock\TimeClockEntry;
use App\Services\TimeClock\TimeClockEntryService;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class MyEntries extends Component
{
    use WithPagination;

    protected TimeClockEntryService $entryService;

    public array $filters = [
        'dateFrom' => '',
        'dateTo' => '',
        'perPage' => 10,
    ];

    public function boot(TimeClockEntryService $entryService): void
    {
        $this->entryService = $entryService;
    }

    public function mount(): void
    {
        Gate::authorize('viewOwn', TimeClockEntry::class);
    }

    public function updatedFilters(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.time-clock.my-entries', [
            'entries' => $this->entryService->paginateOwn((int) auth()->id(), $this->filters),
        ]);
    }
}
