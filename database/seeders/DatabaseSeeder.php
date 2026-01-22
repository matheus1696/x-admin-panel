<?php

namespace Database\Seeders;

use App\Models\Administration\User\User;
use Database\Seeders\Configuration\Establishment\Establishment\DepartmentSeeder;
use Database\Seeders\Configuration\Establishment\Establishment\EstablishmentSeeder;
use Database\Seeders\Configuration\Establishment\EstablishmentType\EstablishmentTypesSeeder;
use Database\Seeders\Configuration\FinancialBlock\FinancialBlockSeeder;
use Database\Seeders\Configuration\Occupation\OccupationSeeder;
use Database\Seeders\Configuration\Region\RegionCitySeeder;
use Database\Seeders\Configuration\Region\RegionCountrySeeder;
use Database\Seeders\Configuration\Region\RegionStateSeeder;
use Database\Seeders\Organization\OrganizationChart\OrganizationChartSeeder;
use Database\Seeders\Organization\Workflow\WorkflowRunStatusSeeder;
use Database\Seeders\Organization\Workflow\WorkflowRunStepStatusSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            OccupationSeeder::class,
            GenderSeeder::class,
            RegionCountrySeeder::class,
            RegionStateSeeder::class,
            RegionCitySeeder::class,
            FinancialBlockSeeder::class,
            EstablishmentTypesSeeder::class,
            EstablishmentSeeder::class,
            DepartmentSeeder::class,
            OrganizationChartSeeder::class,
            WorkflowRunStatusSeeder::class,
            WorkflowRunStepStatusSeeder::class,
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

        if (env('APP_DEBUG')) {
            User::factory(25)->create();
        }
    }
}
