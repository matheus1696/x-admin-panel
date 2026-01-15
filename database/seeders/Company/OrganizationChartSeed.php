<?php

namespace Database\Seeders\Company;

use App\Models\Company\OrganizationChart;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrganizationChartSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpa a tabela antes de popular
        OrganizationChart::truncate();

        // --- NÓ RAIZ ---
        $diretor = OrganizationChart::create([
            'name' => 'Secretário Executivo',
            'parent_id' => null,
        ]);

        // --- PRIMEIRO NÍVEL ---
        $gerenteA = OrganizationChart::create([
            'name' => 'Secretário Execultivo de Atenção Básica e Vigilância em Saúde',
            'parent_id' => $diretor->id,
        ]);

        $gerenteB = OrganizationChart::create([
            'name' => 'Secretário Execultivo de Planejamento e Gestão',
            'parent_id' => $diretor->id,
        ]);

        $gerenteC = OrganizationChart::create([
            'name' => 'Gerente Geral de Gestão',
            'parent_id' => $gerenteB->id,
        ]);

        $gerenteD = OrganizationChart::create([
            'name' => 'Gerente Geral de Atenção à Saúde',
            'parent_id' => $gerenteA->id,
        ]);

        OrganizationChart::create([
            'name' => 'Coordenação de Compras e Suprimentos',
            'parent_id' => $gerenteC->id,
        ]);

        OrganizationChart::create([
            'name' => 'Gerencia de Politicas da Saúde do Homem',
            'parent_id' => $gerenteD->id,
        ]);

        // --- SEGUNDO NÍVEL ---
        $departamentosA = ['Gerencia de Tecnologia da Informação', 'Gerencia do Núcleo de Educação Permanente', 'Gerencia de Planejamento em Saúde'];
        foreach ($departamentosA as $index => $dep) {
            OrganizationChart::create([
                'name' => $dep,
                'parent_id' => $gerenteC->id,
            ]);
        }

        $departamentosB = ['Vendas', 'Marketing', 'Atendimento'];
        foreach ($departamentosB as $index => $dep) {
            OrganizationChart::create([
                'name' => $dep,
                'parent_id' => $gerenteB->id,
            ]);
        }

        $departamentosC = ['Contabilidade', 'Tesouraria', 'Controladoria'];
        foreach ($departamentosC as $index => $dep) {
            OrganizationChart::create([
                'name' => $dep,
                'parent_id' => $gerenteC->id,
            ]);
        }

        // --- TERCEIRO NÍVEL (alguns setores menores) ---
        $subDepartamentos = ['Equipe A', 'Equipe B'];
        $todosDepartamentos = OrganizationChart::whereIn('name', array_merge($departamentosA, $departamentosB, $departamentosC))->get();

        foreach ($todosDepartamentos as $dep) {
            foreach ($subDepartamentos as $index => $sub) {
                OrganizationChart::create([
                    'name' => $sub . ' de ' . $dep->name,
                    'parent_id' => $dep->id,
                ]);
            }
        }
    }
}
