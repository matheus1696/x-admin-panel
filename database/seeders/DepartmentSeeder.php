<?php

namespace Database\Seeders;

use App\Models\Configuration\Establishment\Establishment\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0351',
            'extension' => '0351',
            'type_contact' => 'Main',
            'establishment_id' => 1,
        ]);

        Department::create([
            'title' => 'Farmácia',
            'filter' => 'farmácia',
            'contact' => '(81) 3101-0352',
            'extension' => '0352',
            'type_contact' => 'Internal',
            'establishment_id' => 1,
        ]);

        Department::create([
            'title' => 'Infocras',
            'filter' => 'infocras',
            'contact' => '(81) 3101-0353',
            'extension' => '0353',
            'type_contact' => 'Internal',
            'establishment_id' => 1,
        ]);

        Department::create([
            'title' => 'Almoxarifado',
            'filter' => 'almoxarifado',
            'contact' => null,
            'extension' => null,
            'type_contact' => 'Without',
            'establishment_id' => 1,
        ]);

        Department::create([
            'title' => 'Sala de Exames',
            'filter' => 'sala de exames',
            'contact' => '(81) 3101-0356',
            'extension' => '0356',
            'type_contact' => 'Main',
            'establishment_id' => 2,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0405',
            'extension' => '0405',
            'type_contact' => 'Main',
            'establishment_id' => 3,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0354',
            'extension' => '0354',
            'type_contact' => 'Main',
            'establishment_id' => 4,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0409',
            'extension' => '0409',
            'type_contact' => 'Main',
            'establishment_id' => 5,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0274',
            'extension' => '0274',
            'type_contact' => 'Main',
            'establishment_id' => 6,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0390',
            'extension' => '0390',
            'type_contact' => 'Main',
            'establishment_id' => 7,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0360',
            'extension' => '0360',
            'type_contact' => 'Main',
            'establishment_id' => 9,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0366',
            'extension' => '0366',
            'type_contact' => 'Main',
            'establishment_id' => 10,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0367',
            'extension' => '0367',
            'type_contact' => 'Internal',
            'establishment_id' => 10,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0207',
            'extension' => '0207',
            'type_contact' => 'Main',
            'establishment_id' => 13,
        ]);

        Department::create([
            'title' => 'Gerência',
            'filter' => 'gerência',
            'contact' => '(81) 3101-0208',
            'extension' => '0208',
            'type_contact' => 'Internal',
            'establishment_id' => 13,
        ]);

        Department::create([
            'title' => 'Coordenação',
            'filter' => 'coordenação',
            'contact' => '(81) 3101-0209',
            'extension' => '0209',
            'type_contact' => 'Internal',
            'establishment_id' => 13,
        ]);

        Department::create([
            'title' => 'Farmácia',
            'filter' => 'farmacia',
            'contact' => '(81) 3101-0210',
            'extension' => '0210',
            'type_contact' => 'Internal',
            'establishment_id' => 13,
        ]);

        Department::create([
            'title' => 'Odonto',
            'filter' => 'odonto',
            'contact' => '(81) 3101-0211',
            'extension' => '0211',
            'type_contact' => 'Internal',
            'establishment_id' => 13,
        ]);

        Department::create([
            'title' => 'Estoque Medicamentos',
            'filter' => 'estoque medicamentos',
            'contact' => '(81) 3101-0212',
            'extension' => '0212',
            'type_contact' => 'Internal',
            'establishment_id' => 13,
        ]);

        Department::create([
            'title' => 'Estoque Material',
            'filter' => 'estoque material',
            'contact' => '(81) 3101-0213',
            'extension' => '0213',
            'type_contact' => 'Internal',
            'establishment_id' => 13,
        ]);

        Department::create([
            'title' => 'Remédio na Porta',
            'filter' => 'remédio na porta',
            'contact' => '(81) 3101-0214',
            'extension' => '0214',
            'type_contact' => 'Internal',
            'establishment_id' => 13,
        ]);

        Department::create([
            'title' => 'Administração',
            'filter' => 'administração',
            'contact' => '(81) 3101-0368',
            'extension' => '0368',
            'type_contact' => 'Main',
            'establishment_id' => 11,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0369',
            'extension' => '0369',
            'type_contact' => 'Main',
            'establishment_id' => 12,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0375',
            'extension' => '0375',
            'type_contact' => 'Main',
            'establishment_id' => 23,
        ]);

        Department::create([
            'title' => 'Administração',
            'filter' => 'administração',
            'contact' => '(81) 3101-0374',
            'extension' => '0374',
            'type_contact' => 'Main',
            'establishment_id' => 15,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0373',
            'extension' => '0373',
            'type_contact' => 'Main',
            'establishment_id' => 24,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0384',
            'extension' => '0384',
            'type_contact' => 'Main',
            'establishment_id' => 16,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0382',
            'extension' => '0382',
            'type_contact' => 'Main',
            'establishment_id' => 17,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0378',
            'extension' => '0378',
            'type_contact' => 'Main',
            'establishment_id' => 18,
        ]);

        Department::create([
            'title' => 'Administração',
            'filter' => 'administração',
            'contact' => '(81) 3101-0379',
            'extension' => '0379',
            'type_contact' => 'Internal',
            'establishment_id' => 18,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0380',
            'extension' => '0380',
            'type_contact' => 'Main',
            'establishment_id' => 19,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0358',
            'extension' => '0358',
            'type_contact' => 'Main',
            'establishment_id' => 20,
        ]);

        Department::create([
            'title' => 'Farmácia',
            'filter' => 'farmacia',
            'contact' => '(81) 3101-0359',
            'extension' => '0359',
            'type_contact' => 'Internal',
            'establishment_id' => 20,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0417',
            'extension' => '0417',
            'type_contact' => 'Main',
            'establishment_id' => 21,
        ]);

        Department::create([
            'title' => 'Gerência',
            'filter' => 'gerência',
            'contact' => '(81) 3101-0235',
            'extension' => '0235',
            'type_contact' => 'Internal',
            'establishment_id' => 98,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0376',
            'extension' => '0376',
            'type_contact' => 'Main',
            'establishment_id' => 26,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0210',
            'extension' => '0210',
            'type_contact' => 'Internal',
            'establishment_id' => 27,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0306',
            'extension' => '0306',
            'type_contact' => 'Main',
            'establishment_id' => 28,
        ]);

        Department::create([
            'title' => 'Gerência',
            'filter' => 'gerência',
            'contact' => '(81) 3101-0307',
            'extension' => '0307',
            'type_contact' => 'Internal',
            'establishment_id' => 28,
        ]);

        Department::create([
            'title' => 'Administração',
            'filter' => 'administração',
            'contact' => '(81) 3101-0308',
            'extension' => '0308',
            'type_contact' => 'Internal',
            'establishment_id' => 28,
        ]);

        Department::create([
            'title' => 'Posto de Enfermagem II',
            'filter' => 'posto de enfermagem ii',
            'contact' => '(81) 3101-0309',
            'extension' => '0309',
            'type_contact' => 'Internal',
            'establishment_id' => 28,
        ]);

        Department::create([
            'title' => 'Laboratório',
            'filter' => 'laboratório',
            'contact' => '(81) 3101-0310',
            'extension' => '0310',
            'type_contact' => 'Internal',
            'establishment_id' => 28,
        ]);

        Department::create([
            'title' => 'Serviço Social',
            'filter' => 'serviço social',
            'contact' => '(81) 3101-0311',
            'extension' => '0311',
            'type_contact' => 'Internal',
            'establishment_id' => 28,
        ]);

        Department::create([
            'title' => 'Nutrição',
            'filter' => 'nutrição',
            'contact' => '(81) 3101-0312',
            'extension' => '0312',
            'type_contact' => 'Internal',
            'establishment_id' => 28,
        ]);

        Department::create([
            'title' => 'Farmácia',
            'filter' => 'farmacia',
            'contact' => '(81) 3101-0313',
            'extension' => '0313',
            'type_contact' => 'Internal',
            'establishment_id' => 28,
        ]);

        Department::create([
            'title' => 'CCIH',
            'filter' => 'ccih',
            'contact' => '(81) 3101-0314',
            'extension' => '0314',
            'type_contact' => 'Internal',
            'establishment_id' => 28,
        ]);

        Department::create([
            'title' => 'Pediatria',
            'filter' => 'pediatria',
            'contact' => '(81) 3101-0315',
            'extension' => '0315',
            'type_contact' => 'Internal',
            'establishment_id' => 28,
        ]);

        Department::create([
            'title' => 'Clínica Médica',
            'filter' => 'clínica médica',
            'contact' => '(81) 3101-0316',
            'extension' => '0316',
            'type_contact' => 'Internal',
            'establishment_id' => 28,
        ]);

        Department::create([
            'title' => 'Direção',
            'filter' => 'direção',
            'contact' => '(81) 3101-0317',
            'extension' => '0317',
            'type_contact' => 'Internal',
            'establishment_id' => 28,
        ]);

        Department::create([
            'title' => 'Posto de Enfermagem I',
            'filter' => 'posto de enfermagem i',
            'contact' => '(81) 3101-0318',
            'extension' => '0318',
            'type_contact' => 'Internal',
            'establishment_id' => 28,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0319',
            'extension' => '0319',
            'type_contact' => 'Main',
            'establishment_id' => 28,
        ]);

        Department::create([
            'title' => 'SAME',
            'filter' => 'same',
            'contact' => '(81) 3101-0320',
            'extension' => '0320',
            'type_contact' => 'Internal',
            'establishment_id' => 28,
        ]);

        Department::create([
            'title' => 'Ambulatório',
            'filter' => 'ambulatório',
            'contact' => '(81) 3101-0321',
            'extension' => '0321',
            'type_contact' => 'Internal',
            'establishment_id' => 28,
        ]);

        Department::create([
            'title' => 'Raio X',
            'filter' => 'raio x',
            'contact' => '(81) 3101-0322',
            'extension' => '0322',
            'type_contact' => 'Internal',
            'establishment_id' => 28,
        ]);

        Department::create([
            'title' => 'Faturamento',
            'filter' => 'faturamento',
            'contact' => '(81) 3101-0323',
            'extension' => '0323',
            'type_contact' => 'Internal',
            'establishment_id' => 28,
        ]);

        Department::create([
            'title' => 'RH',
            'filter' => 'rh',
            'contact' => '(81) 3101-0324',
            'extension' => '0324',
            'type_contact' => 'Internal',
            'establishment_id' => 28,
        ]);

        Department::create([
            'title' => 'Manutenção',
            'filter' => 'manutenção',
            'contact' => '(81) 3101-0325',
            'extension' => '0325',
            'type_contact' => 'Internal',
            'establishment_id' => 28,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0385',
            'extension' => '0385',
            'type_contact' => 'Main',
            'establishment_id' => 29,
        ]);

        Department::create([
            'title' => 'Administração',
            'filter' => 'administração',
            'contact' => '(81) 3101-0386',
            'extension' => '0386',
            'type_contact' => 'Internal',
            'establishment_id' => 29,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0387',
            'extension' => '0387',
            'type_contact' => 'Main',
            'establishment_id' => 30,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0388',
            'extension' => '0388',
            'type_contact' => 'Main',
            'establishment_id' => 31,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0389',
            'extension' => '0389',
            'type_contact' => 'Main',
            'establishment_id' => 32,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0391',
            'extension' => '0391',
            'type_contact' => 'Main',
            'establishment_id' => 33,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0392',
            'extension' => '0392',
            'type_contact' => 'Main',
            'establishment_id' => 34,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0393',
            'extension' => '0393',
            'type_contact' => 'Main',
            'establishment_id' => 35,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0394',
            'extension' => '0394',
            'type_contact' => 'Main',
            'establishment_id' => 36,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0395',
            'extension' => '0395',
            'type_contact' => 'Main',
            'establishment_id' => 37,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0396',
            'extension' => '0396',
            'type_contact' => 'Main',
            'establishment_id' => 38,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0397',
            'extension' => '0397',
            'type_contact' => 'Main',
            'establishment_id' => 39,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0398',
            'extension' => '0398',
            'type_contact' => 'Main',
            'establishment_id' => 40,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0399',
            'extension' => '0399',
            'type_contact' => 'Main',
            'establishment_id' => 41,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0400',
            'extension' => '0400',
            'type_contact' => 'Main',
            'establishment_id' => 42,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0401',
            'extension' => '0401',
            'type_contact' => 'Main',
            'establishment_id' => 43,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0402',
            'extension' => '0402',
            'type_contact' => 'Main',
            'establishment_id' => 44,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0403',
            'extension' => '0403',
            'type_contact' => 'Main',
            'establishment_id' => 45,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0404',
            'extension' => '0404',
            'type_contact' => 'Main',
            'establishment_id' => 46,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0406',
            'extension' => '0406',
            'type_contact' => 'Main',
            'establishment_id' => 47,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0407',
            'extension' => '0407',
            'type_contact' => 'Main',
            'establishment_id' => 48,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0408',
            'extension' => '0408',
            'type_contact' => 'Main',
            'establishment_id' => 49,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0410',
            'extension' => '0410',
            'type_contact' => 'Main',
            'establishment_id' => 50,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0411',
            'extension' => '0411',
            'type_contact' => 'Main',
            'establishment_id' => 51,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0412',
            'extension' => '0412',
            'type_contact' => 'Main',
            'establishment_id' => 52,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0413',
            'extension' => '0413',
            'type_contact' => 'Main',
            'establishment_id' => 53,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0414',
            'extension' => '0414',
            'type_contact' => 'Main',
            'establishment_id' => 54,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0415',
            'extension' => '0415',
            'type_contact' => 'Main',
            'establishment_id' => 55,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0416',
            'extension' => '0416',
            'type_contact' => 'Main',
            'establishment_id' => 56,
        ]);

        Department::create([
            'title' => 'Administração',
            'filter' => 'administração',
            'contact' => '(81) 3101-0418',
            'extension' => '0418',
            'type_contact' => 'Internal',
            'establishment_id' => 56,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0419',
            'extension' => '0419',
            'type_contact' => 'Main',
            'establishment_id' => 57,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0420',
            'extension' => '0420',
            'type_contact' => 'Main',
            'establishment_id' => 58,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0421',
            'extension' => '0421',
            'type_contact' => 'Main',
            'establishment_id' => 59,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0422',
            'extension' => '0422',
            'type_contact' => 'Main',
            'establishment_id' => 60,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0423',
            'extension' => '0423',
            'type_contact' => 'Main',
            'establishment_id' => 61,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0424',
            'extension' => '0424',
            'type_contact' => 'Main',
            'establishment_id' => 62,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0425',
            'extension' => '0425',
            'type_contact' => 'Main',
            'establishment_id' => 63,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0426',
            'extension' => '0426',
            'type_contact' => 'Main',
            'establishment_id' => 64,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0427',
            'extension' => '0427',
            'type_contact' => 'Main',
            'establishment_id' => 65,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0428',
            'extension' => '0428',
            'type_contact' => 'Main',
            'establishment_id' => 66,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0429',
            'extension' => '0429',
            'type_contact' => 'Main',
            'establishment_id' => 67,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0430',
            'extension' => '0430',
            'type_contact' => 'Main',
            'establishment_id' => 68,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0431',
            'extension' => '0431',
            'type_contact' => 'Main',
            'establishment_id' => 69,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0432',
            'extension' => '0432',
            'type_contact' => 'Main',
            'establishment_id' => 70,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0433',
            'extension' => '0433',
            'type_contact' => 'Main',
            'establishment_id' => 71,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0434',
            'extension' => '0434',
            'type_contact' => 'Main',
            'establishment_id' => 72,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0435',
            'extension' => '0435',
            'type_contact' => 'Main',
            'establishment_id' => 73,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0436',
            'extension' => '0436',
            'type_contact' => 'Main',
            'establishment_id' => 74,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0437',
            'extension' => '0437',
            'type_contact' => 'Main',
            'establishment_id' => 75,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0438',
            'extension' => '0438',
            'type_contact' => 'Main',
            'establishment_id' => 76,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0439',
            'extension' => '0439',
            'type_contact' => 'Main',
            'establishment_id' => 77,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0440',
            'extension' => '0440',
            'type_contact' => 'Main',
            'establishment_id' => 78,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0441',
            'extension' => '0441',
            'type_contact' => 'Main',
            'establishment_id' => 79,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0442',
            'extension' => '0442',
            'type_contact' => 'Main',
            'establishment_id' => 80,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0443',
            'extension' => '0443',
            'type_contact' => 'Main',
            'establishment_id' => 81,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0444',
            'extension' => '0444',
            'type_contact' => 'Main',
            'establishment_id' => 82,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0445',
            'extension' => '0445',
            'type_contact' => 'Main',
            'establishment_id' => 83,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0446',
            'extension' => '0446',
            'type_contact' => 'Main',
            'establishment_id' => 84,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0447',
            'extension' => '0447',
            'type_contact' => 'Main',
            'establishment_id' => 85,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0448',
            'extension' => '0448',
            'type_contact' => 'Main',
            'establishment_id' => 86,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0449',
            'extension' => '0449',
            'type_contact' => 'Main',
            'establishment_id' => 87,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0450',
            'extension' => '0450',
            'type_contact' => 'Main',
            'establishment_id' => 88,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0451',
            'extension' => '0451',
            'type_contact' => 'Main',
            'establishment_id' => 89,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0452',
            'extension' => '0452',
            'type_contact' => 'Main',
            'establishment_id' => 90,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0453',
            'extension' => '0453',
            'type_contact' => 'Main',
            'establishment_id' => 91,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0454',
            'extension' => '0454',
            'type_contact' => 'Main',
            'establishment_id' => 92,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0455',
            'extension' => '0455',
            'type_contact' => 'Main',
            'establishment_id' => 93,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0456',
            'extension' => '0456',
            'type_contact' => 'Main',
            'establishment_id' => 94,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0457',
            'extension' => '0457',
            'type_contact' => 'Main',
            'establishment_id' => 95,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0458',
            'extension' => '0458',
            'type_contact' => 'Main',
            'establishment_id' => 96,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0459',
            'extension' => '0459',
            'type_contact' => 'Main',
            'establishment_id' => 97,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0460',
            'extension' => '0460',
            'type_contact' => 'Main',
            'establishment_id' => 98,
        ]);

        Department::create([
            'title' => 'Gerência',
            'filter' => 'gerência',
            'contact' => '(81) 3101-0461',
            'extension' => '0461',
            'type_contact' => 'Internal',
            'establishment_id' => 99,
        ]);

        Department::create([
            'title' => 'Administração',
            'filter' => 'administração',
            'contact' => '(81) 3101-0462',
            'extension' => '0462',
            'type_contact' => 'Internal',
            'establishment_id' => 99,
        ]);

        Department::create([
            'title' => 'Recepção',
            'filter' => 'recepção',
            'contact' => '(81) 3101-0463',
            'extension' => '0463',
            'type_contact' => 'Main',
            'establishment_id' => 100,
        ]);

        Department::create([
            'title' => 'Farmácia',
            'filter' => 'farmacia',
            'contact' => '(81) 3101-0464',
            'extension' => '0464',
            'type_contact' => 'Internal',
            'establishment_id' => 100,
        ]);

        Department::create([
            'title' => 'Almoxarifado',
            'filter' => 'almoxarifado',
            'contact' => null,
            'extension' => null,
            'type_contact' => 'Without',
            'establishment_id' => 100,
        ]);

        Department::create([
            'title' => 'Direção',
            'filter' => 'direção',
            'contact' => '(81) 3101-0465',
            'extension' => '0465',
            'type_contact' => 'Internal',
            'establishment_id' => 100,
        ]);

        Department::create([
            'title' => 'RH',
            'filter' => 'rh',
            'contact' => '(81) 3101-0466',
            'extension' => '0466',
            'type_contact' => 'Internal',
            'establishment_id' => 100,
        ]);

        Department::create([
            'title' => 'Faturamento',
            'filter' => 'faturamento',
            'contact' => '(81) 3101-0467',
            'extension' => '0467',
            'type_contact' => 'Internal',
            'establishment_id' => 100,
        ]);

        Department::create([
            'title' => 'Serviço Social',
            'filter' => 'serviço social',
            'contact' => '(81) 3101-0468',
            'extension' => '0468',
            'type_contact' => 'Internal',
            'establishment_id' => 100,
        ]);

        Department::create([
            'title' => 'Nutrição',
            'filter' => 'nutrição',
            'contact' => '(81) 3101-0469',
            'extension' => '0469',
            'type_contact' => 'Internal',
            'establishment_id' => 100,
        ]);

        Department::create([
            'title' => 'Laboratório',
            'filter' => 'laboratório',
            'contact' => '(81) 3101-0470',
            'extension' => '0470',
            'type_contact' => 'Internal',
            'establishment_id' => 100,
        ]);

        Department::create([
            'title' => 'Raio X',
            'filter' => 'raio x',
            'contact' => '(81) 3101-0471',
            'extension' => '0471',
            'type_contact' => 'Internal',
            'establishment_id' => 100,
        ]);

        Department::create([
            'title' => 'Posto de Enfermagem',
            'filter' => 'posto de enfermagem',
            'contact' => '(81) 3101-0472',
            'extension' => '0472',
            'type_contact' => 'Internal',
            'establishment_id' => 100,
        ]);

        Department::create([
            'title' => 'Ambulatório',
            'filter' => 'ambulatório',
            'contact' => '(81) 3101-0473',
            'extension' => '0473',
            'type_contact' => 'Internal',
            'establishment_id' => 100,
        ]);

    }
}
