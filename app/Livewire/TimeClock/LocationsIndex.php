<?php

namespace App\Livewire\TimeClock;

use App\Livewire\Traits\Modal;
use App\Livewire\Traits\WithFlashMessage;
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
        $this->reset(['locationId', 'name', 'latitude', 'longitude']);
        $this->radius_meters = (int) config('time_clock.default_location_radius_meters', 150);
        $this->active = true;
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.time-clock.locations-index', [
            'locations' => $this->locationService->list(),
        ]);
    }
}
