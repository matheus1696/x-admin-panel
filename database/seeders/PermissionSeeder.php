<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        /*
        |--------------------------------------------------------------------------
        | PERMISSÕES
        |--------------------------------------------------------------------------
        */

        $permissions = [

            // Dashboard
            [
                'name' => 'dashboard.view',
                'description' => 'Visualizar Dashboard',
                'translation' => 'Dashboard',
            ],

            // Organização (Governança)
            [
                'name' => 'organization.view',
                'description' => 'Visualizar Organograma',
                'translation' => 'Organograma',
            ],
            [
                'name' => 'organization.manage',
                'description' => 'Gerenciar Organograma',
                'translation' => 'Gerenciar Organograma',
            ],
            [
                'name' => 'workflow.manage',
                'description' => 'Gerenciar Fluxo de Trabalho',
                'translation' => 'Fluxo de Trabalho',
            ],

            // Administração (Cadastros Mestres)
            [
                'name' => 'admin.establishments.manage',
                'description' => 'Gerenciar Estabelecimentos',
                'translation' => 'Estabelecimentos',
            ],
            [
                'name' => 'admin.occupations.manage',
                'description' => 'Gerenciar Ocupações',
                'translation' => 'Ocupações',
            ],
            [
                'name' => 'admin.regions.manage',
                'description' => 'Gerenciar Regiões',
                'translation' => 'Regiões',
            ],
            [
                'name' => 'admin.financial-blocks.manage',
                'description' => 'Gerenciar Blocos Financeiros',
                'translation' => 'Blocos Financeiros',
            ],

            // Usuários & Acessos
            [
                'name' => 'users.view',
                'description' => 'Visualizar Usuários',
                'translation' => 'Listar Usuários',
            ],
            [
                'name' => 'users.create',
                'description' => 'Criar Usuários',
                'translation' => 'Criar Usuários',
            ],
            [
                'name' => 'users.update',
                'description' => 'Editar Usuários',
                'translation' => 'Editar Usuários',
            ],
            [
                'name' => 'users.password',
                'description' => 'Redefinir Senha de Usuários',
                'translation' => 'Redefinir Senha',
            ],
            [
                'name' => 'users.permissions',
                'description' => 'Gerenciar Permissões de Usuários',
                'translation' => 'Permissões de Usuários',
            ],

            // Auditoria
            [
                'name' => 'audit.logs.view',
                'description' => 'Visualizar Logs do Sistema',
                'translation' => 'Logs do Sistema',
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }

        /*
        |--------------------------------------------------------------------------
        | ROLES
        |--------------------------------------------------------------------------
        */

        $superAdmin = Role::firstOrCreate(['name' => 'super-admin'], [
            'type' => 'Administrador do Sistema',
            'description' => 'Acesso total ao sistema',
            'translation' => 'Super Admin',
        ]);

        $admin = Role::firstOrCreate(['name' => 'admin'], [
            'type' => 'Administração',
            'description' => 'Administração do sistema',
            'translation' => 'Administrador',
        ]);

        $manager = Role::firstOrCreate(['name' => 'manager'], [
            'type' => 'Gerência',
            'description' => 'Gerenciamento operacional',
            'translation' => 'Gerente',
        ]);

        $auditor = Role::firstOrCreate(['name' => 'auditor'], [
            'type' => 'Auditoria',
            'description' => 'Auditoria do sistema',
            'translation' => 'Auditor',
        ]);

        $user = Role::firstOrCreate(['name' => 'user'], [
            'type' => 'Usuário',
            'description' => 'Usuário padrão',
            'translation' => 'Usuário',
        ]);

        /*
        |--------------------------------------------------------------------------
        | ATRIBUIÇÃO DE PERMISSÕES
        |--------------------------------------------------------------------------
        */

        // Super Admin → tudo
        $superAdmin->givePermissionTo(Permission::all());

        // Admin (cadastros + organização)
        $admin->givePermissionTo([
            'dashboard.view',
            'organization.view',
            'organization.manage',
            'workflow.manage',
            'admin.establishments.manage',
            'admin.occupations.manage',
            'admin.regions.manage',
            'admin.financial-blocks.manage',
        ]);

        // Gerente (pessoas)
        $manager->givePermissionTo([
            'dashboard.view',
            'users.view',
            'users.create',
            'users.update',
            'users.password',
            'users.permissions',
        ]);

        // Auditor
        $auditor->givePermissionTo([
            'audit.logs.view',
        ]);

        // Usuário comum
        $user->givePermissionTo([
            'dashboard.view',
        ]);
    }
}
