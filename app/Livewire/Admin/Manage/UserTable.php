<?php

namespace App\Livewire\Admin\Manage;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class UserTable extends Component
{
    use WithPagination;

    // Filtros e ordenação
    public $name = '';
    public $email = '';
    public $status = 'all';
    public $sort = 'name_asc';
    public $perPage = 10;

    // Reseta a paginação ao atualizar qualquer propriedade
    public function updated(){
        $this->resetPage();
    }

    public function render(){
        // Consulta base
        $query = User::query();

        // Filtro de nome
        if ($this->name) { $query->where('name_filter', 'like', '%' . strtolower($this->name) . '%'); }

        // Filtro de email
        if ($this->email) { $query->where('email', 'like', '%' . $this->email . '%'); }

        // Filtro de status
        if ($this->status !== 'all') { $query->where('status', $this->status); }

        // Ordenação
        switch ($this->sort) {
            // Nome asc
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            // Nome desc
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            // Email asc
            case 'email_asc':
                $query->orderBy('email', 'asc');
                break;
            // Email desc
            case 'email_desc':
                $query->orderBy('email', 'desc');
                break;
        }

        // Paginação
        $users = $query->paginate($this->perPage);

        // Retorna a view com os usuários
        return view('livewire.admin.manage.user-table', compact('users'));
    }
}
