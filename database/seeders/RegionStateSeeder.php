<?php

namespace Database\Seeders;

use App\Models\Region\RegionState;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RegionStateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        RegionState::create([
            'id'=>26,
            'acronym'=>'PE',
            'state'=>'Pernambuco',
            'filter'=>'pernambuco',
            'code_uf'=>'26',
            'code_ddd'=>'81',
            'country_id'=>74,
        ]);

        RegionState::create([
            'id'=>12,
            'acronym'=>'AC',
            'state'=>'Acre',
            'filter'=>'acre',
            'code_uf'=>'12',
            'country_id'=>74,
        ]);

        RegionState::create([
            'id'=>27,
            'acronym'=>'AL',
            'state'=>'Alagoas',
            'filter'=>'alagoas',
            'code_uf'=>'27',
            'country_id'=>74,
        ]);

        RegionState::create([
            'id'=>16,
            'acronym'=>'AP',
            'state'=>'Amapá',
            'filter'=>'amapá',
            'code_uf'=>'16',
            'country_id'=>74,
        ]);

        RegionState::create([
            'id'=>13,
            'acronym'=>'AM',
            'state'=>'Amazonas',
            'filter'=>'amazonas',
            'code_uf'=>'13',
            'country_id'=>74,
        ]);

        RegionState::create([
            'id'=>29,
            'acronym'=>'BA',
            'state'=>'Bahia',
            'filter'=>'bahia',
            'code_uf'=>'29',
            'country_id'=>74,
        ]);

        RegionState::create([
            'id'=>23,
            'acronym'=>'CE',
            'state'=>'Ceará',
            'filter'=>'ceára',
            'code_uf'=>'23',
            'country_id'=>74,
        ]);

        RegionState::create([
            'id'=>32,
            'acronym'=>'ES',
            'state'=>'Espirito Santo',
            'filter'=>'espirito santo',
            'code_uf'=>'32',
            'country_id'=>74,
        ]);

        RegionState::create([
            'id'=>52,
            'acronym'=>'GO',
            'state'=>'Goías',
            'filter'=>'goías',
            'code_uf'=>'52',
            'country_id'=>74,
        ]);

        RegionState::create([
            'id'=>21,
            'acronym'=>'MA',
            'state'=>'Maranhão',
            'filter'=>'marahão',
            'code_uf'=>'21',
            'country_id'=>74,
        ]);

        RegionState::create([
            'id'=>51,
            'acronym'=>'MT',
            'state'=>'Mato Grosso',
            'filter'=>'Mato Grosso',
            'code_uf'=>'51',
            'country_id'=>74,
        ]);

        RegionState::create([
            'id'=>50,
            'acronym'=>'MS',
            'state'=>'Mato Grosso do Sul',
            'filter'=>'mato grosso do sul',
            'code_uf'=>'50',
            'country_id'=>74,
        ]);

        RegionState::create([
            'id'=>31,
            'acronym'=>'MG',
            'state'=>'Minas Gerais',
            'filter'=>'minas gerais',
            'code_uf'=>'31',
            'country_id'=>74,
        ]);

        RegionState::create([
            'id'=>15,
            'acronym'=>'PA',
            'state'=>'Pará',
            'filter'=>'pará',
            'code_uf'=>'15',
            'country_id'=>74,
        ]);

        RegionState::create([
            'id'=>25,
            'acronym'=>'PB',
            'state'=>'Paraíba',
            'filter'=>'paraíba',
            'code_uf'=>'25',
            'country_id'=>74,
        ]);

        RegionState::create([
            'id'=>41,
            'acronym'=>'PR',
            'state'=>'Paraná',
            'filter'=>'paraná',
            'code_uf'=>'41',
            'country_id'=>74,
        ]);

        RegionState::create([
            'id'=>22,
            'acronym'=>'PI',
            'state'=>'Piauí',
            'filter'=>'piauí',
            'code_uf'=>'22',
            'country_id'=>74,
        ]);

        RegionState::create([
            'id'=>33,
            'acronym'=>'RJ',
            'state'=>'Rio de Janeiro',
            'filter'=>'Rio de Janeiro',
            'code_uf'=>'33',
            'country_id'=>74,
        ]);

        RegionState::create([
            'id'=>24,
            'acronym'=>'RN',
            'state'=>'Rio Grande do Norte',
            'filter'=>'rio grande do norte',
            'code_uf'=>'24',
            'country_id'=>74,
        ]);

        RegionState::create([
            'id'=>43,
            'acronym'=>'RS',
            'state'=>'Rio Grande do Sul',
            'filter'=>'rio grande do sul',
            'code_uf'=>'43',
            'country_id'=>74,
        ]);

        RegionState::create([
            'id'=>11,
            'acronym'=>'RO',
            'state'=>'Rondônia',
            'filter'=>'rondônia',
            'code_uf'=>'11',
            'country_id'=>74,
        ]);

        RegionState::create([
            'id'=>14,
            'acronym'=>'RR',
            'state'=>'Roraima',
            'filter'=>'roraima',
            'code_uf'=>'14',
            'country_id'=>74,
        ]);

        RegionState::create([
            'id'=>42,
            'acronym'=>'SC',
            'state'=>'Santa Catarina',
            'filter'=>'santa catarina',
            'code_uf'=>'42',
            'country_id'=>74,
        ]);

        RegionState::create([
            'id'=>35,
            'acronym'=>'SP',
            'state'=>'São Paulo',
            'filter'=>'são paulo',
            'code_uf'=>'35',
            'country_id'=>74,
        ]);

        RegionState::create([
            'id'=>28,
            'acronym'=>'SE',
            'state'=>'Sergipe',
            'filter'=>'sergipe',
            'code_uf'=>'28',
            'country_id'=>74,
        ]);

        RegionState::create([
            'id'=>17,
            'acronym'=>'TO',
            'state'=>'Tocantins',
            'filter'=>'tocantins',
            'code_uf'=>'17',
            'country_id'=>74,
        ]);

        RegionState::create([
            'id'=>53,
            'acronym'=>'DF',
            'state'=>'Distrito Federal',
            'filter'=>'distrito federal',
            'code_uf'=>'53',
            'country_id'=>74,
        ]);
    }
}
