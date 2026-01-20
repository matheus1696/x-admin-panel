<?php

namespace App\Livewire\Configuration\Establishment\Establishment;

use App\Livewire\Traits\Modal;
use App\Livewire\Traits\WithFlashMessage;
use App\Models\Configuration\Region\RegionCity;
use App\Models\Configuration\Region\RegionState;
use App\Models\Manage\Company\EstablishmentType;
use App\Models\Manage\Company\FinancialBlock;
use App\Services\Configuration\Establishment\Establishment\EstablishmentService;
use App\Validation\Configuration\Establishment\Establishment\EstablishmentRules;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class EstablishmentList extends Component
{
    use WithPagination, WithFlashMessage, Modal;

    protected EstablishmentService $establishmentService;

    /** Filters */
    public array $filters = [
        'code' => '',
        'filter' => '',
        'status' => 'all',
        'sort' => 'name_asc',
        'perPage' => 10,
    ];

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

    public function updatedFilters()
    {
        $this->resetPage();
    }

    /* CREATE */
    public function create(): void
    {
        $this->reset();
        $this->openModal('modal-form-create-establishment');
    }

    public function store(): void
    {
        $data = $this->validate(EstablishmentRules::store()); 
        $this->establishmentService->store($data);
        $this->reset();
        $this->flashSuccess('Setor adicionado no organograma com sucesso.');
        $this->closeModal();
    }

    public function status(int $id): void
    {
        $this->establishmentService->status($id);
        $this->flashSuccess('Setor foi atualizada com sucesso.');
    }

    public function render(): View
    {
        return view('livewire.configuration.establishment.establishment.establishment-list',[
            'establishments' => $this->establishmentService->index($this->filters),
            'states' => RegionState::all(),
            'cities' => RegionCity::all(),
            'establishmentTypes' => EstablishmentType::all(),
            'financialBlocks' => FinancialBlock::all(),
        ])->layout('layouts.app');
    }
}
