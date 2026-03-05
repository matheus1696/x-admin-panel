<?php

use App\Livewire\Administration\Supplier\SupplierPage;
use App\Models\Administration\Supplier\Supplier;
use App\Models\Administration\User\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;

function createSupplierManagerUser(): User
{
    $user = User::factory()->create();

    Permission::findOrCreate('administration.manage.suppliers', 'web');
    $user->givePermissionTo('administration.manage.suppliers');

    return $user;
}

function createSupplierRegion(string $suffix = '01'): array
{
    $countryId = DB::table('region_countries')->insertGetId([
        'uuid' => (string) Str::uuid(),
        'acronym_2' => 'S'.strtoupper(Str::random(1)),
        'acronym_3' => 'S'.$suffix,
        'title' => 'Pais fornecedor '.$suffix,
        'filter' => 'pais fornecedor '.$suffix,
        'country_ing' => 'Supplier country '.$suffix,
        'filter_country_ing' => 'supplier country '.$suffix,
        'code_iso' => 'SP-'.$suffix,
        'code_ddi' => '55'.$suffix,
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $stateId = DB::table('region_states')->insertGetId([
        'uuid' => (string) Str::uuid(),
        'acronym' => 'SF'.$suffix,
        'title' => 'Estado fornecedor '.$suffix,
        'filter' => 'estado fornecedor '.$suffix,
        'code_uf' => 'SU'.$suffix,
        'code_ddd' => '7'.$suffix,
        'is_active' => true,
        'country_id' => $countryId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $cityId = DB::table('region_cities')->insertGetId([
        'uuid' => (string) Str::uuid(),
        'code_ibge' => 'SUP'.$suffix,
        'title' => 'Cidade fornecedor '.$suffix,
        'filter' => 'cidade fornecedor '.$suffix,
        'code_cep' => '40'.$suffix,
        'is_active' => true,
        'state_id' => $stateId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return [$stateId, $cityId];
}

test('supplier page requires permission and renders for authorized users', function () {
    $authorized = createSupplierManagerUser();
    $unauthorized = User::factory()->create();

    $this->actingAs($authorized)
        ->get(route('administration.manage.suppliers'))
        ->assertOk();

    $this->actingAs($unauthorized)
        ->get(route('administration.manage.suppliers'))
        ->assertRedirect(route('dashboard'));
});

test('supplier page creates and updates supplier through livewire', function () {
    $user = createSupplierManagerUser();
    $this->actingAs($user);
    [$stateId, $cityId] = createSupplierRegion('11');
    $cityIdTwo = DB::table('region_cities')->insertGetId([
        'uuid' => (string) Str::uuid(),
        'code_ibge' => 'SUPX11',
        'title' => 'Cidade fornecedor 11B',
        'filter' => 'cidade fornecedor 11b',
        'code_cep' => '4011B',
        'is_active' => true,
        'state_id' => $stateId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    Livewire::test(SupplierPage::class)
        ->call('create')
        ->set('title', 'Fornecedor Alfa')
        ->set('trade_name', 'Alfa Tech')
        ->set('document', '45.723.174/0001-10')
        ->set('email', 'contato@gmail.com')
        ->set('phone', '(71) 99999-1111')
        ->set('phone_secondary', '(71) 98888-2222')
        ->set('address_street', 'Rua Exemplo')
        ->set('address_number', '123')
        ->set('address_district', 'Centro')
        ->set('state_id', $stateId)
        ->set('city_id', $cityId)
        ->set('address_zipcode', '40000-000')
        ->set('is_active', true)
        ->call('store')
        ->assertHasNoErrors();

    $supplier = Supplier::query()->where('title', 'Fornecedor Alfa')->firstOrFail();

    Livewire::test(SupplierPage::class)
        ->call('edit', $supplier->id)
        ->set('title', 'Fornecedor Alfa Atualizado')
        ->set('trade_name', 'Alfa Tech Atualizado')
        ->set('city_id', $cityIdTwo)
        ->set('is_active', false)
        ->call('update')
        ->assertHasNoErrors();

    $supplier->refresh();

    expect($supplier->title)->toBe('Fornecedor Alfa Atualizado')
        ->and($supplier->trade_name)->toBe('Alfa Tech Atualizado')
        ->and((int) $supplier->state_id)->toBe($stateId)
        ->and((int) $supplier->city_id)->toBe($cityIdTwo)
        ->and((int) $supplier->is_active)->toBe(0);
});

test('supplier page validates brazilian cpf/cnpj on document', function () {
    $user = createSupplierManagerUser();
    $this->actingAs($user);
    [$stateId, $cityId] = createSupplierRegion('21');

    Livewire::test(SupplierPage::class)
        ->call('create')
        ->set('title', 'Fornecedor Documento Invalido')
        ->set('document', '123456789')
        ->set('state_id', $stateId)
        ->set('city_id', $cityId)
        ->set('is_active', true)
        ->call('store')
        ->assertHasErrors(['document']);
});
