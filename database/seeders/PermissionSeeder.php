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
        $permissions = 
            [
            'view-dashboard',
                
            //Gerenciameto dos Usuários
            'view-users',
            'create-users',
            'edit-users',
            'password-users',
            'permission-users',

            //Visualização de Logs
            'view-logs'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Criar roles
        $admin = Role::firstOrCreate(['name' => 'super-admin']);
        $managerLog = Role::firstOrCreate(['name' => 'manager-log']); 
        $managerUser = Role::firstOrCreate(['name' => 'manager-user']); 
        $user = Role::firstOrCreate(['name' => 'user']);

        // Dar todas as permissões ao admin
        $admin->givePermissionTo($permissions);

        // Permissões do manager
        $managerUser->givePermissionTo([
            'view-users',
            'create-users',
            'edit-users',
            'password-users',
            'permission-users',
        ]);

        // Permissões do manager
        $managerLog->givePermissionTo([
            'view-logs'
        ]);

        // Permissões do user básico
        $user->givePermissionTo(['view-dashboard']);
    }
}
