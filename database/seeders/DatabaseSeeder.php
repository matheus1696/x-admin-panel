<?php

namespace Database\Seeders;

use App\Models\Administration\User\User;
use Database\Seeders\Administration\Product\ProductDepartmentSeeder;
use Database\Seeders\Administration\Product\ProductMeasureUnitSeeder;
use Database\Seeders\Administration\Product\ProductSeeder;
use Database\Seeders\Administration\Product\ProductTypeSeeder;
use Database\Seeders\Administration\Supplier\SupplierSeeder;
use Database\Seeders\Administration\Task\TaskPrioritySeeder;
use Database\Seeders\Configuration\Establishment\Establishment\DepartmentSeeder;
use Database\Seeders\Configuration\Establishment\Establishment\EstablishmentSeeder;
use Database\Seeders\Configuration\Establishment\EstablishmentType\EstablishmentTypesSeeder;
use Database\Seeders\Configuration\FinancialBlock\FinancialBlockSeeder;
use Database\Seeders\Configuration\Occupation\OccupationSeeder;
use Database\Seeders\Configuration\Region\RegionCitySeeder;
use Database\Seeders\Configuration\Region\RegionCountrySeeder;
use Database\Seeders\Configuration\Region\RegionStateSeeder;
use Database\Seeders\Organization\OrganizationChart\OrganizationChartSeeder;
use Database\Seeders\Organization\Workflow\WorkflowProcessSeeder;
use Database\Seeders\Organization\Workflow\WorkflowStepSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            ProductDepartmentSeeder::class,
            ProductTypeSeeder::class,
            ProductMeasureUnitSeeder::class,
            ProductSeeder::class,
            OccupationSeeder::class,
            GenderSeeder::class,
            RegionCountrySeeder::class,
            RegionStateSeeder::class,
            RegionCitySeeder::class,
            SupplierSeeder::class,
            FinancialBlockSeeder::class,
            EstablishmentTypesSeeder::class,
            EstablishmentSeeder::class,
            DepartmentSeeder::class,
            OrganizationChartSeeder::class,
            WorkflowProcessSeeder::class,
            WorkflowStepSeeder::class,
            TaskPrioritySeeder::class,
        ]);

        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ])->assignRole('super-admin');

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        if (config('app.debug')) {
            User::factory(25)->create();
        }
    }
}
