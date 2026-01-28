<?php

namespace App\Livewire\Configuration\Region;

use App\Models\Configuration\Region\RegionCity;
use Livewire\Component;
use Livewire\WithPagination;

class RegionCityPage extends Component
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
        $query = RegionCity::query();

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

        $cities = $query->paginate($this->perPage);

        return view('livewire.configuration.region.region-city-page',[
            'cities' => $cities
        ])->layout('layouts.app');
    }
}
