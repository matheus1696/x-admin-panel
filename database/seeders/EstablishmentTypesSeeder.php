<?php

namespace Database\Seeders;

use App\Models\Manage\Company\EstablishmentType;
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
            ['id'=>4,  'title'=>'Policlinica', 'status'=>false],
            ['id'=>73, 'title'=>'Pronto Atendimento'],
            ['id'=>68, 'title'=>'Central de Gestao em Saúde'],
            ['id'=>1,  'title'=>'Posto de Saúde', 'status'=>false],
            ['id'=>-1, 'title'=>'Outros', 'status'=>false],
            ['id'=>15, 'title'=>'Unidade Mista', 'status'=>false],
            ['id'=>32, 'title'=>'Unidade Móvel Fluvial', 'status'=>false],
            ['id'=>36, 'title'=>'Clinica/Centro de Especialidade'],
            ['id'=>40, 'title'=>'Unidade Movel Terrestre', 'status'=>false],
            ['id'=>71, 'title'=>'Centro de Apoio à Saúde da Família', 'status'=>false],
            ['id'=>72, 'title'=>'Unidade de Atenção à Saúde Indígena', 'status'=>false],
            ['id'=>74, 'title'=>'Polo Academia Da Saúde', 'status'=>false],
            ['id'=>5,  'title'=>'Hospital Geral'],
            ['id'=>7,  'title'=>'Hospital Especializado', 'status'=>false],
            ['id'=>20, 'title'=>'Pronto Socorro Geral'],
            ['id'=>21, 'title'=>'Pronto Socorro Especializado', 'status'=>false],
            ['id'=>62, 'title'=>'Hospital/Dia - Isolado', 'status'=>false],
            ['id'=>69, 'title'=>'Centro de Atencao Hemoterapia e ou Hematologica', 'status'=>false],
            ['id'=>77, 'title'=>'Serviço de Atenção Domiciliar Isolado(Home Care)', 'status'=>false],
            ['id'=>22, 'title'=>'Consultório Isolado', 'status'=>false],
            ['id'=>39, 'title'=>'Unidade de Apoio Diagnose e Terapia (SADT ISOLADO)'],
            ['id'=>42, 'title'=>'Unidade Móvel de Nível Pré-Hospitalar na Área de Urgência', 'status'=>false],
            ['id'=>43, 'title'=>'Farmácia'],
            ['id'=>50, 'title'=>'Unidade de Vigilância em Saúde'],
            ['id'=>60, 'title'=>'Cooperativa', 'status'=>false],
            ['id'=>61, 'title'=>'Centro de Parto Normal - Isolado', 'status'=>false],
            ['id'=>64, 'title'=>'Central de Regulação de Serviços de Saúde', 'status'=>false],
            ['id'=>67, 'title'=>'Laboratório Central de Saúde Pública - LACEN', 'status'=>false],
            ['id'=>70, 'title'=>'Centro de Atenção Psicossocial'],
            ['id'=>75, 'title'=>'Telessaúde', 'status'=>false],
            ['id'=>76, 'title'=>'Central de Regulação Médica das Urgências', 'status'=>false],
            ['id'=>78, 'title'=>'Unidade de Atenção em Regime Residencial', 'status'=>false],
            ['id'=>79, 'title'=>'Oficina Ortopédica', 'status'=>false],
            ['id'=>80, 'title'=>'Laboratorio de Saúde Publica', 'status'=>false],
            ['id'=>81, 'title'=>'Central de Regulação do Acesso'],
            ['id'=>82, 'title'=>'Central de Notificação, Captação e Distribuição de Orgãos Estadual', 'status'=>false],
            ['id'=>85, 'title'=>'Centro de Imunização', 'status'=>false],
        ];

        foreach ($types as $type) {
            EstablishmentType::create([
                'id'     => $type['id'],
                'title'  => $type['title'],
                'filter' => strtolower($type['title']),
                'status' => $type['status'] ?? true,
            ]);
        }
    }
}
