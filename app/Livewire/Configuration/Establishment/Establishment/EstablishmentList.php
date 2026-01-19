<?php

namespace App\Livewire\Configuration\Establishment\Establishment;

use App\Models\Manage\Company\Establishment;
use Livewire\Component;
use Livewire\WithPagination;

class EstablishmentList extends Component
{
    use WithPagination;

    public $code = '';
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
        // Consulta base
        $query = Establishment::query();
        
        // Filtros 
        if ($this->code) { $query->where('code', 'like', '%' . strtolower($this->code) . '%'); }

        //  
        if ($this->name) { $query->where('filter', 'like', '%' . strtolower($this->name) . '%'); }

        if ($this->status !== 'all') { $query->where('status', $this->status); }

        // Ordenação
        switch ($this->sort) {
            case 'name_asc':
                $query->orderBy('title', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('title', 'desc');
                break;
        }

        // Paginação
        $establishments = $query->paginate($this->perPage);

        return view('livewire.configuration.establishment.establishment.establishment-list',[
            'establishments' => $establishments,
        ])->layout('layouts.app');
    }
}
