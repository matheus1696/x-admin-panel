<?php

namespace App\Livewire\Administration\Supplier;

use App\Livewire\Traits\Modal;
use App\Models\Configuration\Region\RegionCity;
use App\Models\Configuration\Region\RegionState;
use App\Livewire\Traits\WithFlashMessage;
use App\Services\Administration\Supplier\SupplierService;
use App\Services\Administration\Supplier\ViaCepService;
use App\Validation\Administration\Supplier\SupplierRules;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Livewire\Component;

class SupplierPage extends Component
{
    use Modal, WithFlashMessage;

    protected SupplierService $supplierService;
    protected ViaCepService $viaCepService;

    public ?int $supplierId = null;
    public string $title = '';
    public ?string $trade_name = null;
    public ?string $document = null;
    public ?string $email = null;
    public ?string $phone = null;
    public ?string $phone_secondary = null;
    public ?string $address_street = null;
    public ?string $address_number = null;
    public ?string $address_district = null;
    public ?int $state_id = null;
    public ?int $city_id = null;
    public ?string $address_zipcode = null;
    public bool $is_active = true;

    public function boot(SupplierService $supplierService, ViaCepService $viaCepService): void
    {
        $this->supplierService = $supplierService;
        $this->viaCepService = $viaCepService;
    }

    public function create(): void
    {
        $this->resetForm();
        $this->openModal('modal-form-create-supplier');
    }

    public function store(): void
    {
        $data = $this->validate(SupplierRules::store($this->state_id));

        $this->supplierService->create($data);

        $this->flashSuccess('Fornecedor cadastrado com sucesso.');
        $this->closeModal();
        $this->resetForm();
    }

    public function edit(int $id): void
    {
        $supplier = $this->supplierService->find($id);

        $this->supplierId = $supplier->id;
        $this->title = $supplier->title;
        $this->trade_name = $supplier->trade_name;
        $this->document = $supplier->document;
        $this->email = $supplier->email;
        $this->phone = $supplier->phone;
        $this->phone_secondary = $supplier->phone_secondary;
        $this->address_street = $supplier->address_street;
        $this->address_number = $supplier->address_number;
        $this->address_district = $supplier->address_district;
        $this->state_id = $supplier->state_id;
        $this->city_id = $supplier->city_id;
        $this->address_zipcode = $supplier->address_zipcode;
        $this->is_active = (bool) $supplier->is_active;

        $this->openModal('modal-form-edit-supplier');
    }

    public function update(): void
    {
        if (! $this->supplierId) {
            return;
        }

        $data = $this->validate(SupplierRules::update($this->supplierId, $this->state_id));

        $this->supplierService->update($this->supplierId, $data);

        $this->flashSuccess('Fornecedor atualizado com sucesso.');
        $this->closeModal();
        $this->resetForm();
    }

    public function render(): View
    {
        $states = RegionState::query()->where('is_active', true)->orderBy('title')->get();
        $cities = RegionCity::query()
            ->when($this->state_id, fn ($query) => $query->where('state_id', $this->state_id))
            ->where('is_active', true)
            ->orderBy('title')
            ->get();

        return view('livewire.administration.supplier.supplier-page', [
            'suppliers' => $this->supplierService->index(),
            'states' => $states,
            'cities' => $cities,
        ])->layout('layouts.app');
    }

    public function updated(string $property): void
    {
        if ($property === 'state_id') {
            $this->city_id = null;
        }
    }

    public function searchCep(): void
    {
        $result = $this->viaCepService->lookup((string) $this->address_zipcode);

        if ($result['status'] === 'invalid') {
            $this->flashWarning('Informe um CEP válido com 8 dígitos.');
            return;
        }

        if ($result['status'] === 'error') {
            $this->flashWarning('Não foi possível consultar o CEP agora. Continue o preenchimento manual.');
            return;
        }

        if ($result['status'] === 'not_found') {
            $this->flashInfo('CEP não encontrado. Continue o preenchimento manual.');
            return;
        }

        $data = $result['data'] ?? [];

        $this->address_zipcode = $this->formatCep((string) ($data['cep'] ?? $this->address_zipcode));
        $this->address_street = (string) ($data['logradouro'] ?? '');
        $this->address_district = (string) ($data['bairro'] ?? '');

        $uf = mb_strtoupper((string) ($data['uf'] ?? ''));
        $state = RegionState::query()
            ->whereRaw('UPPER(acronym) = ?', [$uf])
            ->first();

        if ($state) {
            $this->state_id = $state->id;

            $cityFilter = Str::ascii(mb_strtolower((string) ($data['localidade'] ?? '')));
            $this->city_id = null;

            $cities = RegionCity::query()
                ->where('state_id', $state->id)
                ->get();

            foreach ($cities as $candidate) {
                $candidateTitle = Str::ascii(mb_strtolower((string) $candidate->title));
                $candidateFilter = Str::ascii(mb_strtolower((string) $candidate->filter));

                if ($candidateTitle === $cityFilter || $candidateFilter === $cityFilter) {
                    $this->city_id = $candidate->id;
                    break;
                }
            }
        } else {
            $this->state_id = null;
            $this->city_id = null;
        }

        $this->flashSuccess('CEP consultado com sucesso.');
    }

    private function resetForm(): void
    {
        $this->reset([
            'supplierId',
            'title',
            'trade_name',
            'document',
            'email',
            'phone',
            'phone_secondary',
            'address_street',
            'address_number',
            'address_district',
            'state_id',
            'city_id',
            'address_zipcode',
        ]);

        $this->is_active = true;
    }

    private function formatCep(string $value): string
    {
        $digits = preg_replace('/\D/', '', $value);
        $digits = substr((string) $digits, 0, 8);

        if (strlen($digits) !== 8) {
            return $value;
        }

        return substr($digits, 0, 5).'-'.substr($digits, 5, 3);
    }
}
