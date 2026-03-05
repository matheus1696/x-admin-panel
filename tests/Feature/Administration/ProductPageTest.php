<?php

use App\Livewire\Administration\Product\ProductPage;
use App\Models\Administration\Product\ProductDepartment;
use App\Models\Administration\Product\Product;
use App\Models\Administration\Product\ProductMeasureUnit;
use App\Models\Administration\Product\ProductType;
use App\Models\Administration\User\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;

function createProductManagerUser(): User
{
    $user = User::factory()->create();

    Permission::findOrCreate('administration.manage.products', 'web');
    $user->givePermissionTo('administration.manage.products');

    return $user;
}

function createProductCatalogDependencies(): array
{
    $department = ProductDepartment::query()->create([
        'code' => 'PATRIMONIO',
        'name' => 'Patrimonio Teste',
    ]);

    $productType = ProductType::query()->create([
        'title' => 'Equipamento Teste',
        'description' => 'Categoria para produtos de teste.',
    ]);

    $measureUnit = ProductMeasureUnit::query()->create([
        'acronym' => 'UN-TST',
        'title' => 'Unidade Teste',
        'base_quantity' => 1,
    ]);

    return [$department, $productType, $measureUnit];
}

test('product page requires permission and renders for authorized users', function () {
    $authorized = createProductManagerUser();
    $unauthorized = User::factory()->create();

    $this->actingAs($authorized)
        ->get(route('administration.manage.products'))
        ->assertOk();

    $this->actingAs($unauthorized)
        ->get(route('administration.manage.products'))
        ->assertRedirect(route('dashboard'));
});

test('product page creates and updates product through livewire', function () {
    $user = createProductManagerUser();
    $this->actingAs($user);
    [$department, $productType, $measureUnit] = createProductCatalogDependencies();

    Livewire::test(ProductPage::class)
        ->call('create')
        ->set('code', 'EQP-001')
        ->set('sku', 'NOTE-001')
        ->set('title', 'Notebook Corporativo')
        ->set('nature', 'ASSET')
        ->set('product_department_id', $department->id)
        ->set('product_type_id', $productType->id)
        ->set('default_measure_unit_id', $measureUnit->id)
        ->set('description', 'Equipamento para uso administrativo.')
        ->call('store')
        ->assertHasNoErrors();

    $product = Product::query()->where('title', 'Notebook Corporativo')->firstOrFail();

    Livewire::test(ProductPage::class)
        ->call('edit', $product->id)
        ->set('code', null)
        ->set('sku', 'NOTE-002')
        ->set('title', 'Notebook Corporativo Atualizado')
        ->set('nature', 'SUPPLY')
        ->set('product_department_id', null)
        ->set('product_type_id', $productType->id)
        ->set('default_measure_unit_id', null)
        ->set('description', 'Equipamento atualizado para uso administrativo.')
        ->call('update')
        ->assertHasNoErrors();

    $product->refresh();

    expect($product->code)->toBeNull()
        ->and($product->sku)->toBe('NOTE-002')
        ->and($product->title)->toBe('Notebook Corporativo Atualizado')
        ->and($product->nature)->toBe('SUPPLY')
        ->and((int) $product->product_department_id)->toBe($department->id)
        ->and((int) $product->product_type_id)->toBe($productType->id)
        ->and($product->default_measure_unit_id)->toBeNull()
        ->and($product->description)->toBe('Equipamento atualizado para uso administrativo.');
});
