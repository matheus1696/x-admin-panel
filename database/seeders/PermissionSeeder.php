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

        //Criação de Permissões

            //Dashboard
            Permission::create(['name' => 'view-dashboard', 'description' => 'Acesso ao Dashboard', 'translation' => 'Visualizar Painel de Controle']);

            //Gerenciameto dos Usuários
            Permission::create(['name' => 'view-users', 'description' => 'Acesso ao Gerenciamento de Usuários', 'translation' => 'Visualizar Usuários']);
            Permission::create(['name' => 'create-users', 'description' => 'Acesso a Criação de Usuários', 'translation' => 'Criar Usuários']);
            Permission::create(['name' => 'edit-users', 'description' => 'Acesso ao Alteração de Usuários', 'translation' => 'Editar Usuários']);
            Permission::create(['name' => 'password-users', 'description' => 'Acesso ao Redefinição de Senha do Usuários', 'translation' => 'Redefinir Senha de Usuários']);
            Permission::create(['name' => 'permission-users', 'description' => 'Acesso ao Gerenciamento de Permissões do Usuários', 'translation' => 'Gerenciar Permissões de Usuários']);

            //Visualização de Logs do Sistema
            Permission::create(['name' => 'view-logs', 'description' => 'Acesso ao Gerenciamento de Logs do Sistema', 'translation' => 'Visualizar Logs']);

        // Criação de Roles
        $admin = Role::firstOrCreate(['name' => 'super-admin', 'type' => 'Administrador do Sistema', 'description' => 'Super Administrador do Sistema', 'translation' => 'Super Administrador']);
        $managerLog = Role::firstOrCreate(['name' => 'manager-log', 'type' => 'Gerenciamento do Sistema', 'description' => 'Gerenciador de Logs do Sistema', 'translation' => 'Gerenciador de Logs']); 
        $managerUser = Role::firstOrCreate(['name' => 'manager-user', 'type' => 'Gerenciamento do Sistema', 'description' => 'Gerenciador de Usuários do Sistema', 'translation' => 'Gerenciador de Usuários']); 
        $user = Role::firstOrCreate(['name' => 'user', 'type' => 'Usuário', 'description' => 'Usuário', 'translation' => 'Usuário']);

        //Atribuindo permissões as Roles
            //Pegar todas as permissões
            $permissions = Permission::all();
            
            // Dar todas as permissões ao admin
            $admin->givePermissionTo($permissions);

            // Permissões do manager user
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
