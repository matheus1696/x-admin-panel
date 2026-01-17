<?php

namespace Database\Seeders\Company;

use App\Models\Company\OrganizationChart;
use Illuminate\Database\Seeder;

class OrganizationChartSeed extends Seeder
{
    public function run(): void
    {
        OrganizationChart::create([
            'title' => 'Secretaria Municipal de Saúde',
            'acronym' => 'SMS',
            'hierarchy'=>'0'
        ]);
    }
}
