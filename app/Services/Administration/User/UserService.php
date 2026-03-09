<?php

namespace App\Services\Administration\User;

use App\Models\Administration\User\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class UserService
{
    public function find(int $id): User
    {
        return User::findOrFail($id);
    }

    public function index(array $filters): LengthAwarePaginator
    {
        $query = User::query();

        if ($filters['name']) {
            $query->where('name_filter', 'like', '%'.strtolower($filters['name']).'%');
        }

        if ($filters['email']) {
            $query->where('email', 'like', '%'.$filters['email'].'%');
        }

        if ($filters['status'] !== 'all') {
            $query->where('is_active', $filters['status']);
        }

        switch ($filters['sort']) {
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

        return $query->paginate($filters['perPage']);
    }

    public function store(array $data): void
    {
        $data['password'] = 'Senha123';

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

        $roles = collect($data['roles'] ?? [])
            ->map(fn ($role) => (string) $role)
            ->filter()
            ->unique()
            ->values();

        $permissions = collect($data['permissions'] ?? [])
            ->map(fn ($permission) => (string) $permission)
            ->filter()
            ->unique()
            ->values();

        $rolePermissions = $roles->isEmpty()
            ? collect()
            : Permission::query()
                ->whereHas('roles', fn ($query) => $query->whereIn('name', $roles->all()))
                ->pluck('name')
                ->unique()
                ->values();

        // Keep as direct permissions only what is not already granted by selected roles.
        $directPermissions = $permissions->diff($rolePermissions)->values();

        DB::transaction(function () use ($user, $roles, $directPermissions): void {
            $user->syncRoles($roles->all());
            $user->syncPermissions($directPermissions->all());
            DB::table('sessions')->where('user_id', $user->id)->delete();
        });
    }

    public function status(int $id): User
    {
        $user = User::findOrFail($id);

        return $user->toggleStatus();
    }
}

