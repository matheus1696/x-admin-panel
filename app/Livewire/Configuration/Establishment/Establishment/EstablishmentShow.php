<?php

namespace App\Livewire\Configuration\Establishment\Establishment;

use App\Models\Manage\Company\Department;
use App\Models\Manage\Company\Establishment;
use Livewire\Component;

class EstablishmentShow extends Component
{
    public $establishment;
    public $departments;

    public function mount($code)
    {
        $this->establishment = Establishment::where('code', $code)->first();
        $this->departments = Department::where('establishment_id', $this->establishment->id)->get();
    }

    public function render()
    {
        return view('livewire.configuration.establishment.establishment.establishment-show')->layout('layouts.app');
    }
}
