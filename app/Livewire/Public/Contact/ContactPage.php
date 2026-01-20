<?php

namespace App\Livewire\Public\Contact;

use App\Livewire\Traits\Modal;
use App\Models\Manage\Company\Establishment;
use Livewire\Component;

class ContactPage extends Component
{
    use Modal;

    public $selectedEstablishment = null;

    public function openDepartments(int $id)
    {
        $this->selectedEstablishment = Establishment::with('departments')->findOrFail($id);

        $this->openModal('modal-info-contact');
    }

    public function render()
    {
        $establishments = Establishment::with('departments')->where('status', true)
        ->orderBy('title')
        ->get();

        return view('livewire.public.contact.contact-page', [
            'establishments' => $establishments,
        ])->layout('layouts.app');
    }
}
