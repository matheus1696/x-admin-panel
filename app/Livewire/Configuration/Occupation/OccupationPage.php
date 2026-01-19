<?php

namespace App\Livewire\Configuration\Occupation;

use App\Models\Configuration\Occupation;
use Livewire\Component;
use Livewire\WithPagination;

class OccupationPage extends Component
{
    use WithPagination;

    // Filtros
    public $code = '';
    public $name = '';
    public $status = 'all';
    public $sort = 'name_asc';
    public $perPage = 10;

    // Resetar paginação ao atualizar filtros
    public function updated()
    {
        $this->resetPage();
    }

    public function render()
    {
        // Consulta base
        $query = Occupation::query();

        // Filtros padrão
        $query->orderBy('status', 'desc');

        // Filtros por código
        if ($this->code) { $query->where('code', 'like', '%' . strtolower($this->code) . '%'); }

        // Filtros por nome
        if ($this->name) { $query->where('filter', 'like', '%' . strtolower($this->name) . '%'); }

        // Filtros por status
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
        $occupations = $query->paginate($this->perPage);

        return view('livewire.configuration.occupation.occupation-page',[
            'occupations' => $occupations,
        ])->layout('layouts.app');
    }
}
