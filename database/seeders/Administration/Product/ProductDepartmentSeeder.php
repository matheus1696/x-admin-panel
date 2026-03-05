<?php

namespace Database\Seeders\Administration\Product;

use App\Models\Administration\Product\ProductDepartment;
use Illuminate\Database\Seeder;

class ProductDepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['code' => 'NUTRICAO', 'name' => 'Nutricao'],
            ['code' => 'PATRIMONIO', 'name' => 'Patrimonio'],
            ['code' => 'ALMOX', 'name' => 'Almoxarifado'],
        ];

        foreach ($departments as $department) {
            ProductDepartment::query()->updateOrCreate(
                ['code' => $department['code']],
                ['name' => $department['name']]
            );
        }
    }
}

