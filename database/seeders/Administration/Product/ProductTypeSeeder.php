<?php

namespace Database\Seeders\Administration\Product;

use App\Models\Administration\Product\ProductType;
use Illuminate\Database\Seeder;

class ProductTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['title' => 'Alimenticio', 'description' => 'Produtos para consumo humano e apoio alimentar.'],
            ['title' => 'Medicamento', 'description' => 'Medicamentos, farmacos e correlatos.'],
            ['title' => 'Material medico hospitalar', 'description' => 'Itens de apoio clinico e assistencial.'],
            ['title' => 'Equipamento', 'description' => 'Bens de uso duravel para operacao institucional.'],
            ['title' => 'Equipamento hospitalar', 'description' => 'Equipamentos para diagnostico, terapia e suporte clinico.'],
            ['title' => 'Equipamento de informatica', 'description' => 'Equipamentos de TI e infraestrutura digital.'],
            ['title' => 'Acessorio de informatica', 'description' => 'Perifericos e itens complementares de TI.'],
            ['title' => 'Rede e telecom', 'description' => 'Infraestrutura de rede, conectividade e comunicacao.'],
            ['title' => 'Material de escritorio', 'description' => 'Suprimentos administrativos e papelaria.'],
            ['title' => 'Material de limpeza', 'description' => 'Produtos de higienizacao, assepsia e limpeza.'],
            ['title' => 'EPI', 'description' => 'Equipamentos de protecao individual.'],
            ['title' => 'Mobiliario', 'description' => 'Moveis e itens de apoio estrutural de ambientes.'],
            ['title' => 'Eletrodomestico', 'description' => 'Equipamentos eletrodomesticos para apoio operacional.'],
            ['title' => 'Ferramenta', 'description' => 'Ferramentas manuais e eletricas para manutencao.'],
            ['title' => 'Pecas e reposicao', 'description' => 'Pecas de substituicao e componentes de manutencao.'],
            ['title' => 'Material eletrico', 'description' => 'Itens para instalacao e manutencao eletrica.'],
            ['title' => 'Hidraulica e manutencao predial', 'description' => 'Itens para infraestrutura predial e reparos.'],
            ['title' => 'Seguranca patrimonial', 'description' => 'Dispositivos de monitoramento e seguranca.'],
            ['title' => 'Impressao e digitalizacao', 'description' => 'Equipamentos e suprimentos para impressao.'],
            ['title' => 'Transporte e movimentacao', 'description' => 'Itens para logistica interna e transporte.'],
            ['title' => 'Laboratorio', 'description' => 'Insumos e equipamentos laboratoriais.'],
            ['title' => 'Odontologico', 'description' => 'Insumos e equipamentos para atendimento odontologico.'],
            ['title' => 'Enfermagem', 'description' => 'Materiais de apoio para procedimentos de enfermagem.'],
            ['title' => 'Copa e cozinha', 'description' => 'Materiais e utensilios para apoio alimentar interno.'],
            ['title' => 'Rouparia e textil', 'description' => 'Itens texteis e de rouparia institucional.'],
            ['title' => 'Audio e video', 'description' => 'Equipamentos para comunicacao e apresentacao audiovisual.'],
            ['title' => 'Automacao e identificacao', 'description' => 'Itens para etiquetagem, rastreio e automacao.'],
            ['title' => 'Consumivel tecnico', 'description' => 'Insumos tecnicos de consumo rapido.'],
            ['title' => 'Patrimonio geral', 'description' => 'Classificacao geral para bens patrimoniais diversos.'],
            ['title' => 'Suprimento geral', 'description' => 'Classificacao geral para itens de consumo diversos.'],
        ];

        foreach ($types as $type) {
            ProductType::query()->updateOrCreate(
                ['title' => $type['title']],
                ['description' => $type['description']]
            );
        }
    }
}
