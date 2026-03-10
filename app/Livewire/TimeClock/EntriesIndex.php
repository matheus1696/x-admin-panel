<?php

namespace App\Livewire\TimeClock;

use App\Enums\TimeClock\TimeClockEntryStatus;
use App\Models\Administration\User\User;
use App\Models\TimeClock\TimeClockEntry;
use App\Services\TimeClock\TimeClockEntryService;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class EntriesIndex extends Component
{
    use WithPagination;

    protected TimeClockEntryService $entryService;

    public array $filters = [
        'dateFrom' => '',
        'dateTo' => '',
        'userId' => '',
        'status' => 'all',
        'perPage' => 10,
    ];

    public function boot(TimeClockEntryService $entryService): void
    {
        $this->entryService = $entryService;
    }

    public function mount(): void
    {
        Gate::authorize('viewAny', TimeClockEntry::class);
    }

    public function updatedFilters(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.time-clock.entries-index', [
            'entries' => $this->entryService->paginateAny($this->filters),
            'users' => User::query()->orderBy('name')->get(['id', 'name']),
            'statuses' => TimeClockEntryStatus::cases(),
        ]);
    }
}
