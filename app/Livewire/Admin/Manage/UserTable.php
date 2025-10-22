<?php

namespace App\Livewire\Admin\Manage;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;

class UserTable extends Component
{
    use WithPagination;

    public $name = '';
    public $email = '';
    public $status = '';
    public $sort = 'name_asc';
    public $perPage = 10;

    public function render()
    {
        $query = User::query();

        if ($this->name) { $query->where('name_filter', 'like', '%' . strtolower($this->name) . '%'); }

        if ($this->email) { $query->where('email', 'like', '%' . $this->email . '%'); }

        if ($this->status) { $query->where('status', $this->status); }

        // Ordenação
        switch ($this->sort) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'email_asc':
                $query->orderBy('email', 'asc');
                break;
            case 'email_desc':
                $query->orderBy('email', 'desc');
                break;
        }

        $users = $query->paginate($this->perPage);

        //Permissões
        $permissions = Permission::all();

        return view('livewire.admin.manage.user-table', compact('users', 'permissions'));
    }
}
