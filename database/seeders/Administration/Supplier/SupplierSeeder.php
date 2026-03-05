<?php

namespace Database\Seeders\Administration\Supplier;

use App\Models\Administration\Supplier\Supplier;
use App\Models\Configuration\Region\RegionCity;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $cities = RegionCity::query()
            ->select(['id', 'state_id'])
            ->orderBy('id')
            ->limit(5)
            ->get()
            ->values();

        $suppliers = [
            [
                'title' => 'Alpha Tecnologia Ltda',
                'trade_name' => 'Alpha Tech',
                'document' => '12.345.678/0001-10',
                'email' => 'contato@alphatech.com.br',
                'phone' => '(11) 3333-1001',
                'phone_secondary' => '(11) 98888-1001',
                'address_street' => 'Rua das Inovacoes',
                'address_number' => '120',
                'address_district' => 'Centro',
                'address_zipcode' => '01001-000',
                'is_active' => true,
            ],
            [
                'title' => 'Nordeste Equipamentos SA',
                'trade_name' => 'Nord Equip',
                'document' => '23.456.789/0001-21',
                'email' => 'vendas@nordequip.com.br',
                'phone' => '(81) 3222-2002',
                'phone_secondary' => '(81) 97777-2002',
                'address_street' => 'Avenida Comercial',
                'address_number' => '450',
                'address_district' => 'Boa Vista',
                'address_zipcode' => '50010-000',
                'is_active' => true,
            ],
            [
                'title' => 'Brasil Office Suprimentos Ltda',
                'trade_name' => 'Brasil Office',
                'document' => '34.567.890/0001-32',
                'email' => 'atendimento@brasiloffice.com.br',
                'phone' => '(71) 3444-3003',
                'phone_secondary' => '(71) 96666-3003',
                'address_street' => 'Travessa do Comercio',
                'address_number' => '89',
                'address_district' => 'Lapa',
                'address_zipcode' => '40020-000',
                'is_active' => true,
            ],
            [
                'title' => 'Sigma Servicos e Solucoes Ltda',
                'trade_name' => 'Sigma Solucoes',
                'document' => '45.678.901/0001-43',
                'email' => 'comercial@sigmasolucoes.com.br',
                'phone' => '(62) 3555-4004',
                'phone_secondary' => '(62) 95555-4004',
                'address_street' => 'Rua Projetada',
                'address_number' => '300',
                'address_district' => 'Setor Sul',
                'address_zipcode' => '74010-000',
                'is_active' => true,
            ],
            [
                'title' => 'Omega Distribuidora Hospitalar Ltda',
                'trade_name' => 'Omega Distribuidora',
                'document' => '56.789.012/0001-54',
                'email' => 'contato@omegadistribuidora.com.br',
                'phone' => '(31) 3666-5005',
                'phone_secondary' => '(31) 94444-5005',
                'address_street' => 'Alameda Principal',
                'address_number' => '780',
                'address_district' => 'Funcionarios',
                'address_zipcode' => '30110-000',
                'is_active' => true,
            ],
        ];

        foreach ($suppliers as $index => $supplier) {
            $city = $cities->get($index);

            Supplier::query()->updateOrCreate(
                ['document' => $supplier['document']],
                array_merge($supplier, [
                    'state_id' => $city?->state_id,
                    'city_id' => $city?->id,
                ])
            );
        }
    }
}

