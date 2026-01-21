<?php

namespace App\Livewire\Configuration\Establishment\Establishment;

use App\Livewire\Traits\Modal;
use App\Livewire\Traits\WithFlashMessage;
use App\Models\Configuration\Establishment\EstablishmentType\EstablishmentType;
use App\Models\Configuration\FinancialBlock\FinancialBlock;
use App\Models\Configuration\Region\RegionCity;
use App\Models\Configuration\Region\RegionState;
use App\Services\Configuration\Establishment\Establishment\DepartmentService;
use App\Services\Configuration\Establishment\Establishment\EstablishmentService;
use App\Validation\Configuration\Establishment\Establishment\DepartmentRules;
use App\Validation\Configuration\Establishment\Establishment\EstablishmentRules;
use Livewire\Component;

class EstablishmentShow extends Component
{
    use WithFlashMessage, Modal;

    protected EstablishmentService $establishmentService;
    protected  DepartmentService $departmentService;

    public $establishmentCNES;
    public ?int $establishmentId = null;
    public ?int $departmentId = null;

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
    public ?string $contact = '';
    public ?string $extension = '';
    public ?string $type_contact = '';
    

    public function boot(EstablishmentService $establishmentService, DepartmentService $departmentService)
    {
        $this->establishmentService = $establishmentService;
        $this->departmentService = $departmentService;
    }

    public function mount($code): void
    {
        $this->establishmentCNES = $code;
    }

    protected function resetForm(): void
    {
        $this->reset([ 'establishmentId', 'code', 'title', 'surname', 'filter', 'address', 'number', 'district', 'city_id', 'state_id', 'latitude', 'longitude', 'type_establishment_id', 'financial_block_id', 'description', 'contact', 'extension', 'type_contact']);
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

    /* CREATE */
    public function createDepartment(): void
    {
        $this->resetForm();
        $this->openModal('modal-form-create-departament');
    }

    public function storeDepartment(): void
    {
        $data = $this->validate(DepartmentRules::store());
        $data['establishment_id'] = $this->establishmentService->show($this->establishmentCNES)->id;
        $this->departmentService->store($data);
        $this->resetForm();
        $this->flashSuccess('Setor adicionado no organograma com sucesso.');
        $this->closeModal();
    }

    /* EDIT */
    public function editDepartment(int $id): void
    {
        $department = $this->departmentService->find($id);

        $this->departmentId    = $department->id;
        $this->title           = $department->title;
        $this->contact         = $department->contact;
        $this->extension       = $department->extension;
        $this->type_contact    = $department->type_contact;

        $this->openModal('modal-form-edit-departament');
    }

    public function updateDepartment(): void
    {
        if (!$this->departmentId) return;

        $data = $this->validate(DepartmentRules::update($this->departmentId));

        $this->departmentService->update($this->departmentId, $data);

        $this->resetForm();
        $this->flashSuccess('Setor alterado no organograma com sucesso.');
        $this->closeModal();
    }

    public function render()
    {
        $establishment = $this->establishmentService->show($this->establishmentCNES);
        $departments = $this->departmentService->index($establishment->id);

        return view('livewire.configuration.establishment.establishment.establishment-show',[
            'establishment' => $establishment,
            'departments' => $departments,
            'states' => RegionState::all(),
            'cities' => RegionCity::all(),
            'establishmentTypes' => EstablishmentType::all(),
            'financialBlocks' => FinancialBlock::all(),
        ])->layout('layouts.app');
    }
}
