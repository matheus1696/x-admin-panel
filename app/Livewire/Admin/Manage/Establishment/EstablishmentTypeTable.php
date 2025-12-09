<?php

namespace App\Livewire\Admin\Manage\Establishment;

use App\Models\Manage\Company\EstablishmentType;
use Livewire\Component;
use Livewire\WithPagination;

class EstablishmentTypeTable extends Component
{
    use WithPagination;

    public $code = '';
    public $name = '';
    public $status = '';
    public $sort = 'name_asc';
    public $perPage = 10;

    public function updated()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = EstablishmentType::query();

        $query->orderBy('status', 'desc');

        if ($this->code) { $query->where('code', 'like', '%' . strtolower($this->code) . '%'); }

        if ($this->name) { $query->where('filter', 'like', '%' . strtolower($this->name) . '%'); }

        if ($this->status) { $query->where('status', $this->status); }

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

        return view('livewire.admin.manage.establishment.establishment-type-table', compact('establishmentTypes'));
    }
}
