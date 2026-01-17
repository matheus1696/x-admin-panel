<?php

namespace Database\Seeders\Organization;

use App\Models\Organization\OrganizationChart\OrganizationChart;
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
