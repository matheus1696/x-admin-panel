<?php

namespace App\Livewire\TimeClock;

use App\Livewire\Traits\Modal;
use App\Livewire\Traits\WithFlashMessage;
use App\Models\Configuration\Establishment\Establishment\Establishment;
use App\Models\TimeClock\TimeClockLocation;
use App\Services\TimeClock\TimeClockLocationService;
use App\Validation\TimeClock\TimeClockLocationRules;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class LocationsIndex extends Component
{
    use Modal;
    use AuthorizesRequests;
    use WithFlashMessage;

    protected TimeClockLocationService $locationService;

    public ?int $locationId = null;

    public string $name = '';

    public ?int $establishment_id = null;

    public ?float $latitude = null;

    public ?float $longitude = null;

    public int $radius_meters = 150;

    public bool|int $active = true;

    public function boot(TimeClockLocationService $locationService): void
    {
        $this->locationService = $locationService;
    }

    public function mount(): void
    {
        $this->authorize('manage', TimeClockLocation::class);
    }

    public function edit(int $locationId): void
    {
        $location = TimeClockLocation::query()->findOrFail($locationId);

        $this->locationId = $location->id;
        $this->name = $location->name;
        $this->establishment_id = $location->establishment_id;
        $this->latitude = (float) $location->latitude;
        $this->longitude = (float) $location->longitude;
        $this->radius_meters = (int) $location->radius_meters;
        $this->active = (bool) $location->active;
        $this->openModal('modal-form-edit-time-clock-location');
    }

    public function create(): void
    {
        $this->resetForm();
        $this->openModal('modal-form-create-time-clock-location');
    }

    public function save(): void
    {
        $data = $this->validate(TimeClockLocationRules::store());
        $data['active'] = (bool) $data['active'];

        if ($this->locationId) {
            $location = TimeClockLocation::query()->findOrFail($this->locationId);
            $this->locationService->update($location, $data);
            $this->flashSuccess('Local atualizado com sucesso.');
        } else {
            $this->locationService->create($data);
            $this->flashSuccess('Local criado com sucesso.');
        }

        $this->closeModal();
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->reset(['locationId', 'name', 'establishment_id', 'latitude', 'longitude']);
        $this->radius_meters = (int) config('time_clock.default_location_radius_meters', 150);
        $this->active = true;
        $this->resetValidation();
    }

    public function updatedEstablishmentId($value): void
    {
        if (! $value) {
            return;
        }

        $establishment = Establishment::query()->find($value);

        if (! $establishment) {
            return;
        }

        $this->name = $establishment->title;

        if (is_numeric($establishment->latitude) && is_numeric($establishment->longitude)) {
            $this->latitude = $this->normalizeCoordinate((string) $establishment->latitude, false);
            $this->longitude = $this->normalizeCoordinate((string) $establishment->longitude, true);
        }
    }

    private function normalizeCoordinate(string $value, bool $isLongitude): ?float
    {
        $normalized = trim($value);

        if ($normalized === '') {
            return null;
        }

        if (str_contains($normalized, '.')) {
            return (float) $normalized;
        }

        $sign = str_starts_with($normalized, '-') ? -1 : 1;
        $digits = ltrim($normalized, '+-');
        $scale = $isLongitude ? 7 : 6;

        if (! ctype_digit($digits) || strlen($digits) <= $scale) {
            return (float) $normalized;
        }

        return $sign * ((int) $digits / (10 ** $scale));
    }

    public function render(): View
    {
        return view('livewire.time-clock.locations-index', [
            'establishments' => Establishment::query()
                ->where('is_active', true)
                ->orderBy('title')
                ->get(['id', 'title', 'latitude', 'longitude']),
            'locations' => $this->locationService->list(),
        ]);
    }
}
