<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpar cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Criar permissões
        $permissions = [
            'view-dashboard',
            'manage-users',
            'view-users', 
            'create-users',
            'edit-users',
            'delete-users',
            'manage-roles',
            'view-reports',
            'manage-settings'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Criar roles
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $manager = Role::firstOrCreate(['name' => 'manager']); 
        $user = Role::firstOrCreate(['name' => 'user']);

        // Dar todas as permissões ao admin
        $admin->givePermissionTo($permissions);

        // Permissões do manager
        $manager->givePermissionTo([
            'view-dashboard',
            'view-users', 
            'view-reports'
        ]);

        // Permissões do user básico
        $user->givePermissionTo(['view-dashboard']);
    }
}
