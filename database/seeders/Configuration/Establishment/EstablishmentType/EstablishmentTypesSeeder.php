<?php

namespace Database\Seeders\Configuration\Establishment\EstablishmentType;

use App\Models\Configuration\Establishment\EstablishmentType\EstablishmentType;
use Illuminate\Database\Seeder;

class EstablishmentTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['id'=>2,  'title'=>'Centro de Saúde/Unidade Basica'],
            ['id'=>4,  'title'=>'Policlinica', 'is_active'=>false],
            ['id'=>73, 'title'=>'Pronto Atendimento'],
            ['id'=>68, 'title'=>'Central de Gestao em Saúde'],
            ['id'=>1,  'title'=>'Posto de Saúde', 'is_active'=>false],
            ['id'=>-1, 'title'=>'Outros', 'is_active'=>false],
            ['id'=>15, 'title'=>'Unidade Mista', 'is_active'=>false],
            ['id'=>32, 'title'=>'Unidade Móvel Fluvial', 'is_active'=>false],
            ['id'=>36, 'title'=>'Clinica/Centro de Especialidade'],
            ['id'=>40, 'title'=>'Unidade Movel Terrestre', 'is_active'=>false],
            ['id'=>71, 'title'=>'Centro de Apoio à Saúde da Família', 'is_active'=>false],
            ['id'=>72, 'title'=>'Unidade de Atenção à Saúde Indígena', 'is_active'=>false],
            ['id'=>74, 'title'=>'Polo Academia Da Saúde', 'is_active'=>false],
            ['id'=>5,  'title'=>'Hospital Geral'],
            ['id'=>7,  'title'=>'Hospital Especializado', 'is_active'=>false],
            ['id'=>20, 'title'=>'Pronto Socorro Geral'],
            ['id'=>21, 'title'=>'Pronto Socorro Especializado', 'is_active'=>false],
            ['id'=>62, 'title'=>'Hospital/Dia - Isolado', 'is_active'=>false],
            ['id'=>69, 'title'=>'Centro de Atencao Hemoterapia e ou Hematologica', 'is_active'=>false],
            ['id'=>77, 'title'=>'Serviço de Atenção Domiciliar Isolado(Home Care)', 'is_active'=>false],
            ['id'=>22, 'title'=>'Consultório Isolado', 'is_active'=>false],
            ['id'=>39, 'title'=>'Unidade de Apoio Diagnose e Terapia (SADT ISOLADO)'],
            ['id'=>42, 'title'=>'Unidade Móvel de Nível Pré-Hospitalar na Área de Urgência', 'is_active'=>false],
            ['id'=>43, 'title'=>'Farmácia'],
            ['id'=>50, 'title'=>'Unidade de Vigilância em Saúde'],
            ['id'=>60, 'title'=>'Cooperativa', 'is_active'=>false],
            ['id'=>61, 'title'=>'Centro de Parto Normal - Isolado', 'is_active'=>false],
            ['id'=>64, 'title'=>'Central de Regulação de Serviços de Saúde', 'is_active'=>false],
            ['id'=>67, 'title'=>'Laboratório Central de Saúde Pública - LACEN', 'is_active'=>false],
            ['id'=>70, 'title'=>'Centro de Atenção Psicossocial'],
            ['id'=>75, 'title'=>'Telessaúde', 'is_active'=>false],
            ['id'=>76, 'title'=>'Central de Regulação Médica das Urgências', 'is_active'=>false],
            ['id'=>78, 'title'=>'Unidade de Atenção em Regime Residencial', 'is_active'=>false],
            ['id'=>79, 'title'=>'Oficina Ortopédica', 'is_active'=>false],
            ['id'=>80, 'title'=>'Laboratorio de Saúde Publica', 'is_active'=>false],
            ['id'=>81, 'title'=>'Central de Regulação do Acesso'],
            ['id'=>82, 'title'=>'Central de Notificação, Captação e Distribuição de Orgãos Estadual', 'is_active'=>false],
            ['id'=>85, 'title'=>'Centro de Imunização', 'is_active'=>false],
        ];

        foreach ($types as $type) {
            EstablishmentType::create([
                'id'     => $type['id'],
                'title'  => $type['title'],
                'filter' => strtolower($type['title']),
                'is_active' => $type['is_active'] ?? true,
            ]);
        }
    }
}
