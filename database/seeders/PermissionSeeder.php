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
        // Limpa cache de permissões
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        /*
        |--------------------------------------------------------------------------
        | PERMISSÕES
        |--------------------------------------------------------------------------
        */

        $permissions = [
            // Dashboard
            [
                'name' => 'dashboard-view',
                'description' => 'Acesso ao Dashboard',
                'translation' => 'Visualizar Painel de Controle',
            ],

            // Estabelecimentos
            [
                'name' => 'establishment-type-view',
                'description' => 'Gerenciar Tipos de Estabelecimento',
                'translation' => 'Tipos de Estabelecimento',
            ],
            [
                'name' => 'establishment-view',
                'description' => 'Gerenciar Estabelecimentos',
                'translation' => 'Estabelecimentos',
            ],

            // Bloco financeiro
            [
                'name' => 'financial-block-view',
                'description' => 'Gerenciar Blocos Financeiros',
                'translation' => 'Blocos Financeiros',
            ],

            // Usuários
            [
                'name' => 'user-view',
                'description' => 'Visualizar Usuários',
                'translation' => 'Visualizar Usuários',
            ],
            [
                'name' => 'user-create',
                'description' => 'Criar Usuários',
                'translation' => 'Criar Usuários',
            ],
            [
                'name' => 'user-edit',
                'description' => 'Editar Usuários',
                'translation' => 'Editar Usuários',
            ],
            [
                'name' => 'user-password',
                'description' => 'Redefinir Senha',
                'translation' => 'Redefinir Senha',
            ],
            [
                'name' => 'user-permission',
                'description' => 'Gerenciar Permissões',
                'translation' => 'Permissões de Usuários',
            ],

            // Regiões
            [
                'name' => 'region-view',
                'description' => 'Configurar Regiões',
                'translation' => 'Regiões',
            ],

            // Ocupações
            [
                'name' => 'occupation-view',
                'description' => 'Configurar Ocupações',
                'translation' => 'Ocupações',
            ],

            // Logs
            [
                'name' => 'log-view',
                'description' => 'Visualizar Logs',
                'translation' => 'Logs do Sistema',
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission['name']], $permission);
        }

        /*
        |--------------------------------------------------------------------------
        | ROLES
        |--------------------------------------------------------------------------
        */

        $admin = Role::firstOrCreate([
            'name' => 'super-admin',
        ], [
            'type' => 'Administrador do Sistema',
            'description' => 'Super Administrador',
            'translation' => 'Super Admin',
        ]);

        $configuration = Role::firstOrCreate([
            'name' => 'configuration',
        ], [
            'type' => 'Configuração',
            'description' => 'Configuração do Sistema',
            'translation' => 'Configuração do Sistema',
        ]);

        $manager = Role::firstOrCreate([
            'name' => 'manager',
        ], [
            'type' => 'Gerenciamento',
            'description' => 'Gerenciamento do Sistema',
            'translation' => 'Gerenciamento',
        ]);

        $audit = Role::firstOrCreate([
            'name' => 'audit',
        ], [
            'type' => 'Auditoria',
            'description' => 'Auditoria do Sistema',
            'translation' => 'Auditoria',
        ]);

        $user = Role::firstOrCreate([
            'name' => 'user',
        ], [
            'type' => 'Usuário',
            'description' => 'Usuário do Sistema',
            'translation' => 'Usuário',
        ]);

        /*
        |--------------------------------------------------------------------------
        | ATRIBUIÇÃO DE PERMISSÕES
        |--------------------------------------------------------------------------
        */

        // Super Admin → TODAS
        $admin->givePermissionTo(Permission::all());

        // Configuração
        $configuration->givePermissionTo([
            'establishment-type-view',
            'establishment-view',
            'region-view',
            'occupation-view',
            'financial-block-view',
        ]);

        // Gerente
        $manager->givePermissionTo([
            'user-view',
            'user-create',
            'user-edit',
            'user-password',
            'user-permission',
        ]);

        // Auditor
        $audit->givePermissionTo([
            'log-view',
        ]);

        // Usuário comum
        $user->givePermissionTo([
            'dashboard-view',
        ]);
    }
}