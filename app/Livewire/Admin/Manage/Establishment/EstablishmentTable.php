<?php

namespace App\Livewire\Admin\Manage\Establishment;

use App\Models\Manage\Company\Establishment;
use Livewire\Component;
use Livewire\WithPagination;

class EstablishmentTable extends Component
{
    use WithPagination;

    public $name = '';
    public $email = '';
    public $status = '';
    public $sort = 'name_asc';
    public $perPage = 10;

    public function updated()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Establishment::query();

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

        $establishments = $query->paginate($this->perPage);

        return view('livewire.admin.manage.establishment.establishment-table', compact('establishments'));
    }
}
