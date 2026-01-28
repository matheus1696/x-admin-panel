<?php

namespace App\Livewire\Configuration\Region;

use App\Models\Configuration\Region\RegionState;
use Livewire\Component;
use Livewire\WithPagination;

class RegionStatePage extends Component
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
        $query = RegionState::query();

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

        $states = $query->paginate($this->perPage);

        return view('livewire.configuration.region.region-state-page',[
            'states' => $states
        ])->layout('layouts.app');
    }
}
