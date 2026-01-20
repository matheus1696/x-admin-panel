<?php

namespace App\Livewire\Configuration\Establishment\Establishment;

use App\Livewire\Traits\Modal;
use App\Livewire\Traits\WithFlashMessage;
use App\Models\Configuration\Region\RegionCity;
use App\Models\Configuration\Region\RegionState;
use App\Models\Manage\Company\Department;
use App\Models\Manage\Company\Establishment;
use App\Models\Manage\Company\EstablishmentType;
use App\Models\Manage\Company\FinancialBlock;
use App\Services\Configuration\Establishment\Establishment\EstablishmentService;
use App\Validation\Configuration\Establishment\Establishment\EstablishmentRules;
use Livewire\Component;

class EstablishmentShow extends Component
{
    use WithFlashMessage, Modal;

    protected EstablishmentService $establishmentService;

    public $establishment;
    public $departments;

    public ?int $establishmentId = null;

    public ?string $code = null;
    public string $title = '';
    public ?string $surname = null;
    public string $filter = '';
    public string $address = '';
    public string $number = '';
    public string $district = '';
    public ?int $city_id = null;
    public ?int $state_id = null;
    public ?string $latitude = null;
    public ?string $longitude = null;
    public ?int $type_establishment_id = null;
    public ?int $financial_block_id = null;
    public ?string $description = null;

    public function boot(EstablishmentService $establishmentService)
    {
        $this->establishmentService = $establishmentService;
    }

    protected function resetForm(): void
    {
        $this->reset([ 'establishmentId', 'code', 'title', 'surname', 'filter', 'address', 'number', 'district', 'city_id', 'state_id', 'latitude', 'longitude', 'type_establishment_id', 'financial_block_id', 'description', ]);
    }

    public function mount($code)
    {
        $this->establishment = Establishment::where('code', $code)->first();
        $this->departments = Department::where('establishment_id', $this->establishment->id)->get();
    }

    /* EDIT */
    public function edit(int $id): void
    {
        $establishment = $this->establishmentService->find($id);

        $this->establishmentId = $establishment->id;
        $this->code = $establishment->code;
        $this->title = $establishment->title;
        $this->surname = $establishment->surname;
        $this->filter = $establishment->filter;
        $this->address = $establishment->address;
        $this->number = $establishment->number;
        $this->district = $establishment->district;
        $this->city_id = $establishment->city_id;
        $this->state_id = $establishment->state_id;
        $this->latitude = $establishment->latitude;
        $this->longitude = $establishment->longitude;
        $this->type_establishment_id = $establishment->type_establishment_id;
        $this->financial_block_id = $establishment->financial_block_id;
        $this->description = $establishment->description;

        $this->openModal('modal-form-edit-establishment');
    }

    public function update(): void
    {
        if (!$this->establishmentId) return;
        $data = $this->validate(EstablishmentRules::update($this->establishmentId));
        $this->establishmentService->update($this->establishmentId, $data);
        $this->resetForm();
        $this->flashSuccess('Setor alterado no organograma com sucesso.');
        $this->closeModal();
    }

    public function status(int $id): void
    {
        $this->establishmentService->status($id);
        $this->flashSuccess('Setor foi atualizada com sucesso.');
    }

    public function render()
    {
        return view('livewire.configuration.establishment.establishment.establishment-show',[
            'states' => RegionState::all(),
            'cities' => RegionCity::all(),
            'establishmentTypes' => EstablishmentType::all(),
            'financialBlocks' => FinancialBlock::all(),
        ])->layout('layouts.app');
    }
}
