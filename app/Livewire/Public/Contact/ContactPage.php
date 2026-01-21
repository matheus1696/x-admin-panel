<?php

namespace App\Livewire\Public\Contact;

use App\Livewire\Traits\Modal;
use App\Models\Configuration\Establishment\Establishment\Department;
use App\Models\Configuration\Establishment\Establishment\Establishment;
use Livewire\Component;

class ContactPage extends Component
{
    use Modal;

    public $searchEstablishment;
    public $searchDepartment;

    public ?int $selectedEstablishmentId = null;
    public $establishmentTitle = null;
    public $departments = null;

    public function loadDepartments()
    {
        $query = Department::where('establishment_id', $this->selectedEstablishmentId);

        if ($this->searchDepartment) {
            $query->where('filter', 'like', '%' . strtolower($this->searchDepartment) . '%');
        }

        $this->departments = $query->orderBy('title')->get();
    }

    public function openDepartments($id)
    {
        $this->selectedEstablishmentId = $id;
        $this->establishmentTitle = Establishment::find($id)->title;
        $this->loadDepartments();
        $this->openModal('modal-info-contact');
    }

    public function updatedSearchDepartment(): void
    {
        $this->loadDepartments();
    }

    public function render()
    {
        $query = Establishment::with('departments');

        if ($this->searchEstablishment) {
            $query->where('filter', 'like', '%' . strtolower($this->searchEstablishment) . '%');
        }       
        
        $establishments = $query->where('status', true)->orderBy('title')->get();

        return view('livewire.public.contact.contact-page', [
            'establishments' => $establishments,
        ])->layout('layouts.app');
    }
}
