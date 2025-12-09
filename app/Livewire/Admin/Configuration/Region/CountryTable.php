<?php

namespace App\Livewire\Admin\Configuration\Region;

use App\Models\Configuration\Region\RegionCountry;
use Livewire\Component;
use Livewire\WithPagination;

class CountryTable extends Component
{
    use WithPagination;

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
        $query = RegionCountry::query();

        $query->orderBy('status', 'desc');

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

        $countries = $query->paginate($this->perPage);

        return view('livewire.admin.configuration.region.country-table', compact('countries'));
    }
}
