<?php

namespace Database\Seeders\Administration\Product;

use App\Models\Administration\Product\ProductMeasureUnit;
use Illuminate\Database\Seeder;

class ProductMeasureUnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            // Unidade base
            ['acronym' => 'UND', 'title' => 'Unidade', 'base_quantity' => 1],
            ['acronym' => 'PAR', 'title' => 'Par', 'base_quantity' => 2],
            ['acronym' => 'DZ', 'title' => 'Duzia', 'base_quantity' => 12],
            ['acronym' => 'CENTO', 'title' => 'Cento', 'base_quantity' => 100],
            ['acronym' => 'MIL', 'title' => 'Milheiro', 'base_quantity' => 1000],

            // Kits e conjuntos
            ['acronym' => 'KIT/2', 'title' => 'Kit com 2', 'base_quantity' => 2],
            ['acronym' => 'KIT/3', 'title' => 'Kit com 3', 'base_quantity' => 3],
            ['acronym' => 'KIT/5', 'title' => 'Kit com 5', 'base_quantity' => 5],
            ['acronym' => 'KIT/10', 'title' => 'Kit com 10', 'base_quantity' => 10],
            ['acronym' => 'KIT/20', 'title' => 'Kit com 20', 'base_quantity' => 20],
            ['acronym' => 'CJ/2', 'title' => 'Conjunto com 2', 'base_quantity' => 2],
            ['acronym' => 'CJ/4', 'title' => 'Conjunto com 4', 'base_quantity' => 4],
            ['acronym' => 'CJ/6', 'title' => 'Conjunto com 6', 'base_quantity' => 6],
            ['acronym' => 'CJ/10', 'title' => 'Conjunto com 10', 'base_quantity' => 10],
            ['acronym' => 'CJ/12', 'title' => 'Conjunto com 12', 'base_quantity' => 12],

            // Pacotes
            ['acronym' => 'PCT/2', 'title' => 'Pacote com 2', 'base_quantity' => 2],
            ['acronym' => 'PCT/4', 'title' => 'Pacote com 4', 'base_quantity' => 4],
            ['acronym' => 'PCT/6', 'title' => 'Pacote com 6', 'base_quantity' => 6],
            ['acronym' => 'PCT/8', 'title' => 'Pacote com 8', 'base_quantity' => 8],
            ['acronym' => 'PCT/10', 'title' => 'Pacote com 10', 'base_quantity' => 10],
            ['acronym' => 'PCT/12', 'title' => 'Pacote com 12', 'base_quantity' => 12],
            ['acronym' => 'PCT/15', 'title' => 'Pacote com 15', 'base_quantity' => 15],
            ['acronym' => 'PCT/20', 'title' => 'Pacote com 20', 'base_quantity' => 20],
            ['acronym' => 'PCT/25', 'title' => 'Pacote com 25', 'base_quantity' => 25],
            ['acronym' => 'PCT/30', 'title' => 'Pacote com 30', 'base_quantity' => 30],
            ['acronym' => 'PCT/50', 'title' => 'Pacote com 50', 'base_quantity' => 50],
            ['acronym' => 'PCT/100', 'title' => 'Pacote com 100', 'base_quantity' => 100],
            ['acronym' => 'PCT/200', 'title' => 'Pacote com 200', 'base_quantity' => 200],

            // Caixas
            ['acronym' => 'CX/2', 'title' => 'Caixa com 2', 'base_quantity' => 2],
            ['acronym' => 'CX/6', 'title' => 'Caixa com 6', 'base_quantity' => 6],
            ['acronym' => 'CX/10', 'title' => 'Caixa com 10', 'base_quantity' => 10],
            ['acronym' => 'CX/12', 'title' => 'Caixa com 12', 'base_quantity' => 12],
            ['acronym' => 'CX/20', 'title' => 'Caixa com 20', 'base_quantity' => 20],
            ['acronym' => 'CX/24', 'title' => 'Caixa com 24', 'base_quantity' => 24],
            ['acronym' => 'CX/25', 'title' => 'Caixa com 25', 'base_quantity' => 25],
            ['acronym' => 'CX/30', 'title' => 'Caixa com 30', 'base_quantity' => 30],
            ['acronym' => 'CX/40', 'title' => 'Caixa com 40', 'base_quantity' => 40],
            ['acronym' => 'CX/50', 'title' => 'Caixa com 50', 'base_quantity' => 50],
            ['acronym' => 'CX/60', 'title' => 'Caixa com 60', 'base_quantity' => 60],
            ['acronym' => 'CX/100', 'title' => 'Caixa com 100', 'base_quantity' => 100],
            ['acronym' => 'CX/200', 'title' => 'Caixa com 200', 'base_quantity' => 200],
            ['acronym' => 'CX/500', 'title' => 'Caixa com 500', 'base_quantity' => 500],

            // Fardos e volumes
            ['acronym' => 'FD/6', 'title' => 'Fardo com 6', 'base_quantity' => 6],
            ['acronym' => 'FD/12', 'title' => 'Fardo com 12', 'base_quantity' => 12],
            ['acronym' => 'FD/24', 'title' => 'Fardo com 24', 'base_quantity' => 24],
            ['acronym' => 'FD/48', 'title' => 'Fardo com 48', 'base_quantity' => 48],
            ['acronym' => 'FD/100', 'title' => 'Fardo com 100', 'base_quantity' => 100],
            ['acronym' => 'ENV/10', 'title' => 'Envelope com 10', 'base_quantity' => 10],
            ['acronym' => 'ENV/50', 'title' => 'Envelope com 50', 'base_quantity' => 50],
            ['acronym' => 'ENV/100', 'title' => 'Envelope com 100', 'base_quantity' => 100],
            ['acronym' => 'BL/10', 'title' => 'Blister com 10', 'base_quantity' => 10],
            ['acronym' => 'BL/20', 'title' => 'Blister com 20', 'base_quantity' => 20],
            ['acronym' => 'SCH/10', 'title' => 'Sache com 10', 'base_quantity' => 10],
            ['acronym' => 'SCH/20', 'title' => 'Sache com 20', 'base_quantity' => 20],
            ['acronym' => 'TP/2', 'title' => 'Tubo com 2', 'base_quantity' => 2],
            ['acronym' => 'TP/6', 'title' => 'Tubo com 6', 'base_quantity' => 6],
            ['acronym' => 'TP/12', 'title' => 'Tubo com 12', 'base_quantity' => 12],
            ['acronym' => 'RL/5', 'title' => 'Rolo com 5', 'base_quantity' => 5],
            ['acronym' => 'RL/10', 'title' => 'Rolo com 10', 'base_quantity' => 10],
            ['acronym' => 'RL/25', 'title' => 'Rolo com 25', 'base_quantity' => 25],
            ['acronym' => 'RL/50', 'title' => 'Rolo com 50', 'base_quantity' => 50],
            ['acronym' => 'REF/5', 'title' => 'Refil com 5', 'base_quantity' => 5],
            ['acronym' => 'REF/10', 'title' => 'Refil com 10', 'base_quantity' => 10],

            // Medidas continuas normalizadas para base de calculo
            ['acronym' => 'KG', 'title' => 'Quilograma', 'base_quantity' => 1],
            ['acronym' => 'G', 'title' => 'Grama', 'base_quantity' => 1],
            ['acronym' => 'L', 'title' => 'Litro', 'base_quantity' => 1],
            ['acronym' => 'ML', 'title' => 'Mililitro', 'base_quantity' => 1],
            ['acronym' => 'M', 'title' => 'Metro', 'base_quantity' => 1],
            ['acronym' => 'CM', 'title' => 'Centimetro', 'base_quantity' => 1],
            ['acronym' => 'M2', 'title' => 'Metro quadrado', 'base_quantity' => 1],
            ['acronym' => 'M3', 'title' => 'Metro cubico', 'base_quantity' => 1],
        ];

        foreach ($units as $unit) {
            ProductMeasureUnit::query()->updateOrCreate(
                ['acronym' => $unit['acronym']],
                $unit
            );
        }
    }
}
