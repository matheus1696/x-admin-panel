<?php

namespace App\Livewire\TimeClock;

use App\DTOs\TimeClock\RegisterTimeClockEntryDTO;
use App\Enums\TimeClock\TimeClockEntryStatus;
use App\Livewire\Traits\WithFlashMessage;
use App\Models\TimeClock\TimeClockEntry;
use App\Models\TimeClock\TimeClockLocation;
use App\Services\TimeClock\TimeClockEntryService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class RegisterEntry extends Component
{
    use WithFileUploads;
    use WithFlashMessage;

    protected TimeClockEntryService $entryService;

    public $photo = null;

    public ?float $latitude = null;

    public ?float $longitude = null;

    public ?float $accuracy = null;

    public ?int $locationId = null;

    public string $locationCaptureState = 'idle';

    public bool $showRegisterModal = false;

    public function boot(TimeClockEntryService $entryService): void
    {
        $this->entryService = $entryService;
    }

    public function mount(): void
    {
        Gate::authorize('register', TimeClockEntry::class);
    }

    public function openRegisterModal(): void
    {
        $this->showRegisterModal = true;
    }

    public function closeRegisterModal(): void
    {
        $this->showRegisterModal = false;
    }

    public function requestAllowance(): void
    {
        $this->flashInfo('Fluxo de solicitacao de abono ainda nao foi implementado.');
    }

    public function register(): void
    {
        $data = $this->validate([
            'photo' => ['nullable', 'image', 'max:5120'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'accuracy' => ['nullable', 'numeric'],
            'locationId' => ['nullable', 'integer', 'exists:time_clock_locations,id'],
        ]);

        $entry = $this->entryService->register(new RegisterTimeClockEntryDTO(
            userId: (int) auth()->id(),
            occurredAt: CarbonImmutable::now(),
            photo: $data['photo'] ?? null,
            latitude: isset($data['latitude']) ? (float) $data['latitude'] : null,
            longitude: isset($data['longitude']) ? (float) $data['longitude'] : null,
            accuracy: isset($data['accuracy']) ? (float) $data['accuracy'] : null,
            deviceMeta: [
                'ip' => request()->ip(),
                'user_agent' => (string) request()->userAgent(),
            ],
            status: TimeClockEntryStatus::OK->value,
            locationId: $data['locationId'] ?? null,
        ));

        $this->reset(['photo', 'latitude', 'longitude', 'accuracy', 'locationId']);
        $this->locationCaptureState = 'idle';
        $this->showRegisterModal = false;

        if ($entry->status === TimeClockEntryStatus::OK->value) {
            $this->flashSuccess('Registro de ponto realizado com sucesso.');

            return;
        }

        $this->flashError('Registro salvo com pendencias de captura.');
    }

    public function render(): View
    {
        $locations = TimeClockLocation::query()
            ->with('establishment')
            ->where('active', true)
            ->orderBy('name')
            ->get();

        return view('livewire.time-clock.register-entry', [
            'locations' => $locations,
            'monthLabel' => now()->translatedFormat('F \\d\\e Y'),
            'monthlyEntries' => $this->entryService->monthlySummary((int) auth()->id()),
        ]);
    }
}
