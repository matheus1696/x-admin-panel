<?php

namespace Database\Seeders\Administration\Product;

use App\Models\Administration\Product\Product;
use App\Models\Administration\Product\ProductDepartment;
use App\Models\Administration\Product\ProductMeasureUnit;
use App\Models\Administration\Product\ProductType;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $typeIds = ProductType::query()->pluck('id', 'title');
        $departmentIds = ProductDepartment::query()->pluck('id', 'code');
        $unitIds = ProductMeasureUnit::query()->pluck('id', 'acronym');

        $catalog = [
            [
                'type' => 'Equipamento de informatica',
                'prefix' => 'TI-EQP',
                'nature' => 'ASSET',
                'unit' => 'UN',
                'items' => [
                    'Notebook Corporativo 14', 'Notebook Corporativo 15', 'Desktop Mini', 'Desktop Torre', 'Servidor Rack',
                    'Monitor LED 21.5', 'Monitor LED 24', 'Monitor LED 27', 'Dock Station USB-C', 'Switch 24 Portas',
                ],
            ],
            [
                'type' => 'Acessorio de informatica',
                'prefix' => 'TI-ACS',
                'nature' => 'SUPPLY',
                'unit' => 'UN',
                'items' => [
                    'Teclado USB', 'Mouse USB', 'Mouse sem fio', 'Headset USB', 'Webcam Full HD',
                    'Fonte para Notebook', 'Memoria RAM 8GB DDR4', 'Memoria RAM 16GB DDR4', 'SSD 480GB', 'HD Externo 1TB',
                ],
            ],
            [
                'type' => 'Impressao e digitalizacao',
                'prefix' => 'IMP',
                'nature' => 'ASSET',
                'unit' => 'UN',
                'items' => [
                    'Impressora Laser Monocromatica', 'Impressora Multifuncional', 'Scanner de Mesa', 'Etiquetadora Termica',
                    'Leitor de Codigo de Barras',
                ],
            ],
            [
                'type' => 'Consumivel tecnico',
                'prefix' => 'CON-TI',
                'nature' => 'SUPPLY',
                'unit' => 'CX/10',
                'items' => [
                    'Toner Preto Laser', 'Toner Ciano Laser', 'Toner Magenta Laser', 'Toner Amarelo Laser',
                    'Cartucho Tinta Preto', 'Cartucho Tinta Colorido', 'Etiqueta Termica 100x50', 'Papel Fotografico A4',
                ],
            ],
            [
                'type' => 'Material de escritorio',
                'prefix' => 'ESC',
                'nature' => 'SUPPLY',
                'unit' => 'CX/50',
                'items' => [
                    'Papel A4 Sulfite', 'Caneta Esferografica Azul', 'Caneta Esferografica Preta', 'Lapis HB',
                    'Borracha Escolar', 'Marcador Permanente', 'Grampeador Medio', 'Clips Metalico',
                    'Pasta Suspensa', 'Envelope Oficio',
                ],
            ],
            [
                'type' => 'Material de limpeza',
                'prefix' => 'LMP',
                'nature' => 'SUPPLY',
                'unit' => 'CX/12',
                'items' => [
                    'Detergente Neutro', 'Desinfetante Hospitalar', 'Agua Sanitaria', 'Alcool 70', 'Pano Multiuso',
                    'Saco de Lixo 50L', 'Saco de Lixo 100L', 'Sabao em Po', 'Papel Toalha', 'Papel Higienico',
                ],
            ],
            [
                'type' => 'EPI',
                'prefix' => 'EPI',
                'nature' => 'SUPPLY',
                'unit' => 'CX/100',
                'items' => [
                    'Luva de Procedimento', 'Mascara Cirurgica', 'Touca Descartavel', 'Avental Descartavel', 'Protetor Facial',
                    'Oculos de Protecao', 'Respirador PFF2', 'Prope Descartavel',
                ],
            ],
            [
                'type' => 'Material medico hospitalar',
                'prefix' => 'MED-MAT',
                'nature' => 'SUPPLY',
                'unit' => 'CX/100',
                'items' => [
                    'Seringa 5ml', 'Seringa 10ml', 'Agulha 25x7', 'Agulha 30x7', 'Equipo Macrogotas',
                    'Gaze Esteril', 'Esparadrapo', 'Curativo Adesivo', 'Cateter Intravenoso', 'Fita Microporosa',
                ],
            ],
            [
                'type' => 'Medicamento',
                'prefix' => 'MED',
                'nature' => 'SUPPLY',
                'unit' => 'CX/20',
                'items' => [
                    'Paracetamol 500mg', 'Dipirona 500mg', 'Ibuprofeno 600mg', 'Amoxicilina 500mg',
                    'Omeprazol 20mg', 'Loratadina 10mg', 'Soro Fisiologico 500ml', 'Glicose 5 por cento 500ml',
                ],
            ],
            [
                'type' => 'Alimenticio',
                'prefix' => 'ALI',
                'nature' => 'SUPPLY',
                'unit' => 'PCT/10',
                'items' => [
                    'Arroz Branco 5kg', 'Feijao Carioca 1kg', 'Acucar Cristal 1kg', 'Cafe Torrado 500g',
                    'Leite em Po Integral', 'Biscoito Agua e Sal', 'Macarrao Espaguete 500g', 'Oleo de Soja 900ml',
                ],
            ],
            [
                'type' => 'Mobiliario',
                'prefix' => 'MOB',
                'nature' => 'ASSET',
                'unit' => 'UN',
                'items' => [
                    'Cadeira Ergonomica', 'Mesa de Escritorio', 'Armario de Aco', 'Arquivo de Gavetas',
                    'Estante Metalica', 'Longarina 3 Lugares', 'Balcao de Atendimento', 'Gaveteiro Volante',
                ],
            ],
            [
                'type' => 'Eletrodomestico',
                'prefix' => 'ELD',
                'nature' => 'ASSET',
                'unit' => 'UN',
                'items' => [
                    'Geladeira 300L', 'Micro-ondas 30L', 'Bebedouro Coluna', 'Purificador de Agua',
                    'Ventilador de Coluna', 'Ar Condicionado 12000 BTUs', 'Ar Condicionado 18000 BTUs',
                ],
            ],
            [
                'type' => 'Ferramenta',
                'prefix' => 'FER',
                'nature' => 'ASSET',
                'unit' => 'UN',
                'items' => [
                    'Furadeira de Impacto', 'Parafusadeira', 'Jogo de Chaves de Fenda', 'Jogo de Chaves Allen',
                    'Alicate Universal', 'Martelo Unha', 'Trena 5m', 'Escada Aluminio 7 Degraus',
                ],
            ],
            [
                'type' => 'Material eletrico',
                'prefix' => 'ELE',
                'nature' => 'SUPPLY',
                'unit' => 'PCT/10',
                'items' => [
                    'Cabo Flexivel 2.5mm', 'Disjuntor 20A', 'Tomada 2P+T', 'Interruptor Simples',
                    'Conector de Emenda', 'Fita Isolante', 'Lampada LED 12W', 'Canaleta PVC',
                ],
            ],
            [
                'type' => 'Hidraulica e manutencao predial',
                'prefix' => 'HID',
                'nature' => 'SUPPLY',
                'unit' => 'UN',
                'items' => [
                    'Torneira de Bancada', 'Sifao Sanfonado', 'Registro Esfera 25mm', 'Joelho PVC 25mm',
                    'Tubo PVC 25mm', 'Veda Rosca', 'Silicone Vedante', 'Valvula de Descarga',
                ],
            ],
            [
                'type' => 'Seguranca patrimonial',
                'prefix' => 'SEG',
                'nature' => 'ASSET',
                'unit' => 'UN',
                'items' => [
                    'Camera de Seguranca IP', 'DVR 8 Canais', 'NVR 16 Canais', 'Sensor de Presenca',
                    'Central de Alarme', 'Controle de Acesso Biomtrico', 'Fechadura Eletronica',
                ],
            ],
            [
                'type' => 'Rede e telecom',
                'prefix' => 'TEL',
                'nature' => 'ASSET',
                'unit' => 'UN',
                'items' => [
                    'Roteador Wi-Fi', 'Access Point Corporativo', 'Telefone IP', 'Radio Comunicador',
                    'Patch Panel 24 Portas', 'Rack de Piso 24U', 'Nobreak 1200VA', 'Nobreak 2000VA',
                ],
            ],
            [
                'type' => 'Copa e cozinha',
                'prefix' => 'CPC',
                'nature' => 'SUPPLY',
                'unit' => 'PCT/50',
                'items' => [
                    'Copo Descartavel 200ml', 'Prato Descartavel', 'Talher Descartavel', 'Guardanapo de Papel',
                    'Detergente para Louca', 'Esponja de Cozinha', 'Saco para Alimentos',
                ],
            ],
            [
                'type' => 'Laboratorio',
                'prefix' => 'LAB',
                'nature' => 'SUPPLY',
                'unit' => 'CX/100',
                'items' => [
                    'Tubo de Ensaio', 'Lamina para Microscopia', 'Pipeta Descartavel', 'Frasco Coletor',
                    'Swab Esteril', 'Reagente Diagnostico', 'Placa de Petri',
                ],
            ],
            [
                'type' => 'Odontologico',
                'prefix' => 'ODO',
                'nature' => 'SUPPLY',
                'unit' => 'CX/50',
                'items' => [
                    'Anestesico Odontologico', 'Resina Composta', 'Broca Odontologica', 'Algodao Odontologico',
                    'Sugador Descartavel', 'Escova de Profilaxia', 'Fio Dental Profissional',
                ],
            ],
            [
                'type' => 'Enfermagem',
                'prefix' => 'ENF',
                'nature' => 'SUPPLY',
                'unit' => 'PCT/100',
                'items' => [
                    'Lanceta Esteril', 'Termometro Clinico Digital', 'Aparelho de Pressao Adulto',
                    'Estetoscopio', 'Oximetro de Dedo', 'Curativo Hidrocoloide', 'Atadura Crepe',
                ],
            ],
            [
                'type' => 'Audio e video',
                'prefix' => 'AVD',
                'nature' => 'ASSET',
                'unit' => 'UN',
                'items' => [
                    'Projetor Multimidia', 'Tela de Projecao', 'Caixa de Som Ativa', 'Microfone Sem Fio',
                    'Mesa de Som Compacta', 'Suporte para Projetor',
                ],
            ],
            [
                'type' => 'Automacao e identificacao',
                'prefix' => 'AUT',
                'nature' => 'ASSET',
                'unit' => 'UN',
                'items' => [
                    'Coletor de Dados Portatil', 'Impressora de Etiquetas Industrial', 'Leitor RFID',
                    'Etiquetas RFID UHF', 'Gateway IoT',
                ],
            ],
            [
                'type' => 'Transporte e movimentacao',
                'prefix' => 'TRN',
                'nature' => 'ASSET',
                'unit' => 'UN',
                'items' => [
                    'Carrinho de Carga', 'Carrinho Plataforma', 'Paleteira Manual', 'Container Plastico Empilhavel',
                    'Caixa Organizadora Grande',
                ],
            ],
            [
                'type' => 'Rouparia e textil',
                'prefix' => 'TXT',
                'nature' => 'SUPPLY',
                'unit' => 'PCT/20',
                'items' => [
                    'Lencol Hospitalar', 'Fronha Hospitalar', 'Toalha de Banho', 'Toalha de Rosto',
                    'Cobertor Hospitalar', 'Avental de Tecido',
                ],
            ],
        ];

        $globalIndex = 1;

        foreach ($catalog as $group) {
            $typeId = $typeIds[$group['type']] ?? null;
            $unitId = $unitIds[$group['unit']] ?? null;

            foreach ($group['items'] as $itemTitle) {
                $sku = sprintf('%s-%04d', $group['prefix'], $globalIndex);
                $code = sprintf('PRD-%04d', $globalIndex);
                $typeTitleLower = mb_strtolower($group['type']);
                $departmentCode = str_contains($typeTitleLower, 'aliment')
                    ? 'NUTRICAO'
                    : (($group['nature'] === 'ASSET' || str_contains($typeTitleLower, 'equipamento'))
                        ? 'PATRIMONIO'
                        : 'ALMOX');

                Product::query()->updateOrCreate(
                    ['sku' => $sku],
                    [
                        'code' => $code,
                        'title' => $itemTitle,
                        'nature' => $group['nature'],
                        'product_department_id' => $departmentIds[$departmentCode] ?? null,
                        'product_type_id' => $typeId,
                        'default_measure_unit_id' => $unitId,
                        'description' => 'Produto catalogado para uso institucional: '.$itemTitle.'.',
                    ]
                );

                $globalIndex++;
            }
        }
    }
}
