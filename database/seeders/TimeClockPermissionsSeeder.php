<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class TimeClockPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            [
                'name' => 'time_clock.register',
                'description' => 'Registrar ponto',
                'translation' => 'Registrar ponto',
            ],
            [
                'name' => 'time_clock.view_own',
                'description' => 'Visualizar os proprios registros de ponto',
                'translation' => 'Visualizar meus registros',
            ],
            [
                'name' => 'time_clock.view_any',
                'description' => 'Visualizar registros de ponto',
                'translation' => 'Visualizar registros',
            ],
            [
                'name' => 'time_clock.reports.view',
                'description' => 'Visualizar relatorios de ponto',
                'translation' => 'Relatorios de ponto',
            ],
            [
                'name' => 'time_clock.export',
                'description' => 'Exportar registros de ponto',
                'translation' => 'Exportar registros',
            ],
            [
                'name' => 'time_clock.locations.manage',
                'description' => 'Gerenciar locais de ponto',
                'translation' => 'Gerenciar locais',
            ],
            [
                'name' => 'time_clock.settings.manage',
                'description' => 'Gerenciar configuracoes de ponto',
                'translation' => 'Gerenciar configuracoes',
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::query()->firstOrCreate(
                ['name' => $permission['name']],
                $permission,
            );
        }

        $superAdmin = Role::query()->where('name', 'super-admin')->first();

        if ($superAdmin) {
            $superAdmin->givePermissionTo(array_column($permissions, 'name'));
        }
    }
}
