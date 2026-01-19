<?php

namespace App\Services\Administration\User;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class UserService
{
    public function find(int $id): User
    {
        return User::findOrFail($id);
    }

    public function index(array $filters): LengthAwarePaginator
    {
        // Consulta base
        $query = User::query();

        // Filtro de nome
        if ($filters['name']) { $query->where('name_filter', 'like', '%' . strtolower($filters['name']) . '%'); }

        // Filtro de email
        if ($filters['email']) { $query->where('email', 'like', '%' . $filters['email'] . '%'); }

        // Filtro de status
        if ($filters['status'] !== 'all') { $query->where('status', $filters['status']); }

        // Ordenação
        switch ($filters['sort']) {
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

        return $query->paginate($filters['perPage']);
    }

    public function store(array $data): void
    {
        $password = 'Senha123';
        $data['password'] = $password;
        User::create($data);
    }

    public function update(int $id, array $data): void
    {
        $user = User::findOrFail($id);
        $user->update($data);
    }

    public function permissionUpdate(int $id, array $data): void
    {
        $user = User::findOrFail($id);

        // Sincroniza as permissões do usuário
        $user->syncPermissions($data);

        // Logout de todas as sessões do usuário
        DB::table('sessions')->where('user_id', $user->id)->delete();
    }

    public function status(int $id): User
    {
        $user = User::findOrFail($id);
        return $user->toggleStatus();
    }
}