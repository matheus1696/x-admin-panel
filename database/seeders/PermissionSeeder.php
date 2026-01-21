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

            // Organização (Governança)
            [
                'name' => 'organization.chart.dashboard.view',
                'description' => 'Visualizar Organograma',
                'translation' => 'Organograma',
            ],
            [
                'name' => 'organization.chart.config.manage',
                'description' => 'Gerenciar Organograma',
                'translation' => 'Gerenciar Organograma',
            ],
            [
                'name' => 'organization.workflow.manage',
                'description' => 'Gerenciar Fluxo de Trabalho',
                'translation' => 'Fluxo de Trabalho',
            ],

            // Configuração do Sistema (Cadastros Mestres)
            [
                'name' => 'config.establishments.manage',
                'description' => 'Gerenciar Estabelecimentos',
                'translation' => 'Estabelecimentos',
            ],
            [
                'name' => 'config.occupations.manage',
                'description' => 'Gerenciar Ocupações',
                'translation' => 'Ocupações',
            ],
            [
                'name' => 'config.regions.manage',
                'description' => 'Gerenciar Regiões',
                'translation' => 'Regiões',
            ],
            [
                'name' => 'config.financial-blocks.manage',
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
            'type' => 'Administração do sistema',
            'description' => 'Administração do sistema',
            'translation' => 'Administração do sistema',
        ]);

        $config = Role::firstOrCreate(['name' => 'config'], [
            'type' => 'Configuração do sistema',
            'description' => 'Configuração do sistema',
            'translation' => 'Configuração do sistema',
        ]);

        $organization = Role::firstOrCreate(['name' => 'organization'], [
            'type' => 'Gestão da Organização',
            'description' => 'Gestão da Organização',
            'translation' => 'Gestão da Organização',
        ]);

        $audit = Role::firstOrCreate(['name' => 'audit'], [
            'type' => 'Auditoria do sistema',
            'description' => 'Auditoria do sistema',
            'translation' => 'Auditoria do sistema',
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

        // Administração de Sistema
        $admin->givePermissionTo([
            'users.view',
            'users.permissions',
        ]);

        // Configuração do Sistema
        $config->givePermissionTo([
            'config.establishments.manage',
            'config.occupations.manage',
            'config.regions.manage',
            'config.financial-blocks.manage',
        ]);

        // Organização
        $organization->givePermissionTo([
            'organization.chart.dashboard.view',
            'organization.chart.config.manage',
            'organization.workflow.manage',
        ]);

        // Auditoria
        $audit->givePermissionTo([
            'audit.logs.view',
        ]);
    }
}
