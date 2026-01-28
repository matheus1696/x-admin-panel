<?php

namespace App\Livewire\Configuration\Establishment\EstablishmentType;

use App\Models\Configuration\Establishment\EstablishmentType\EstablishmentType;
use Livewire\Component;
use Livewire\WithPagination;

class EstablishmentTypePage extends Component
{
    use WithPagination;

    public $name = '';
    public $status = 'all';
    public $sort = 'name_asc';
    public $perPage = 10;

    public function updated()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = EstablishmentType::query();

        $query->orderBy('is_active', 'desc');

        if ($this->name) { $query->where('filter', 'like', '%' . strtolower($this->name) . '%'); }

        if ($this->status !== 'all') { $query->where('is_active', $this->status); }

        // Ordenação
        switch ($this->sort) {
            case 'name_asc':
                $query->orderBy('title', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('title', 'desc');
                break;
        }

        $establishmentTypes = $query->paginate($this->perPage);

        return view('livewire.configuration.establishment.establishment-type.establishment-type-page',[
            'establishmentTypes' => $establishmentTypes,
        ])->layout('layouts.app');
    }
}
