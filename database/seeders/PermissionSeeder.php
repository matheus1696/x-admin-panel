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
                'name' => 'organization.manage.chart',
                'description' => 'Gerenciar Organograma',
                'translation' => 'Gerenciar Organograma',
            ],
            [
                'name' => 'organization.manage.workflow',
                'description' => 'Gerenciar Fluxo de Trabalho',
                'translation' => 'Fluxo de Trabalho',
            ],

            // Configuração do Sistema (Cadastros Mestres)
            [
                'name' => 'configuration.manage.establishments',
                'description' => 'Gerenciar Estabelecimentos',
                'translation' => 'Estabelecimentos',
            ],
            [
                'name' => 'configuration.manage.occupations',
                'description' => 'Gerenciar Ocupações',
                'translation' => 'Ocupações',
            ],
            [
                'name' => 'configuration.manage.regions',
                'description' => 'Gerenciar Regiões',
                'translation' => 'Regiões',
            ],
            [
                'name' => 'configuration.manage.financial-blocks',
                'description' => 'Gerenciar Blocos Financeiros',
                'translation' => 'Blocos Financeiros',
            ],

            // Tarefas
            [
                'name' => 'administration.manage.task',
                'description' => 'Gerencia Tarefas',
                'translation' => 'Gerencia Tarefas',
            ],

            // Usuários & Acessos
            [
                'name' => 'administration.manage.users',
                'description' => 'Visualizar Usuários',
                'translation' => 'Listar Usuários',
            ],
            [
                'name' => 'administration.manage.users.permissions',
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

        $administration = Role::firstOrCreate(['name' => 'administration'], [
            'type' => 'Administração do sistema',
            'description' => 'Administração do sistema',
            'translation' => 'Administração do sistema',
        ]);

        $configuration = Role::firstOrCreate(['name' => 'config'], [
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
        $administration->givePermissionTo([
            'administration.manage.users',
            'administration.manage.users.permissions',
            'administration.manage.task'
        ]);

        // Configuração do Sistema
        $configuration->givePermissionTo([
            'configuration.manage.establishments',
            'configuration.manage.occupations',
            'configuration.manage.regions',
            'configuration.manage.financial-blocks',
        ]);

        // Organização
        $organization->givePermissionTo([
            'organization.manage.chart',
            'organization.manage.workflow',
        ]);

        // Auditoria
        $audit->givePermissionTo([
            'audit.logs.view',
        ]);
    }
}
