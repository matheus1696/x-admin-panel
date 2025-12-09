<?php

namespace Database\Seeders;

use App\Models\Manage\Company\FinancialBlock;
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
            'color'=>'bg-green-600',
            'acronym'=>'ADM',
        ]);

        FinancialBlock::create([
            'title'=>'Atenção Especializada',
            'color'=>'bg-red-600',
            'acronym'=>'ATE',
        ]);

        FinancialBlock::create([
            'title'=>'Atenção Básica',
            'color'=>'bg-blue-600',
            'acronym'=>'ATB',
        ]);

        FinancialBlock::create([
            'title'=>'Vigilância Epdemiológica',
            'color'=>'bg-green-600',
            'acronym'=>'VEPD',
        ]);

        FinancialBlock::create([
            'title'=>'Vigilância Sanitária',
            'color'=>'bg-green-600',
            'acronym'=>'VSAN',
        ]);
    }
}
