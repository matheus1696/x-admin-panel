<?php

namespace Database\Seeders;

use App\Models\Configuration\FinancialBlock\FinancialBlock;
use Illuminate\Database\Seeder;

class FinancialBlockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        FinancialBlock::create([
            'title'=>'Administração',
            'filter'=>'administração',
            'color'=>'bg-green-600',
            'acronym'=>'ADM',
        ]);

        FinancialBlock::create([
            'title'=>'Atenção Especializada',
            'filter'=>'atenção especializada',
            'color'=>'bg-red-600',
            'acronym'=>'ATE',
        ]);

        FinancialBlock::create([
            'title'=>'Atenção Básica',
            'filter'=>'atenção básica',
            'color'=>'bg-blue-600',
            'acronym'=>'ATB',
        ]);

        FinancialBlock::create([
            'title'=>'Vigilância Epdemiológica',
            'filter'=>'vigilância epidemiológica',
            'color'=>'bg-green-600',
            'acronym'=>'VEPD',
        ]);

        FinancialBlock::create([
            'title'=>'Vigilância Sanitária',
            'filter'=>'vigilância sanitária',
            'color'=>'bg-green-600',
            'acronym'=>'VSAN',
        ]);
    }
}
