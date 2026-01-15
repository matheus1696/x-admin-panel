<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\Company\OrganizationChartSeed;
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
            OrganizationChartSeed::class,
        ]);

        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ])->assignRole('super-admin');

        User::factory(25)->create();
    }
}
