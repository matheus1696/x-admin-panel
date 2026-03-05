<?php

use App\Livewire\Administration\Product\ProductTypePage;
use App\Models\Administration\Product\ProductType;
use App\Models\Administration\User\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;

function createProductTypeManagerUser(): User
{
    $user = User::factory()->create();

    Permission::findOrCreate('administration.manage.product-types', 'web');
    $user->givePermissionTo('administration.manage.product-types');

    return $user;
}

test('product type page requires permission and renders for authorized users', function () {
    $authorized = createProductTypeManagerUser();
    $unauthorized = User::factory()->create();

    $this->actingAs($authorized)
        ->get(route('administration.manage.product-types'))
        ->assertOk();

    $this->actingAs($unauthorized)
        ->get(route('administration.manage.product-types'))
        ->assertRedirect(route('dashboard'));
});

test('product type page creates and updates through livewire', function () {
    $user = createProductTypeManagerUser();
    $this->actingAs($user);

    Livewire::test(ProductTypePage::class)
        ->call('create')
        ->set('title', 'Equipamento')
        ->set('description', 'Produtos de hardware e equipamentos.')
        ->call('store')
        ->assertHasNoErrors();

    $productType = ProductType::query()->where('title', 'Equipamento')->firstOrFail();

    Livewire::test(ProductTypePage::class)
        ->call('edit', $productType->id)
        ->set('title', 'Equipamento Hospitalar')
        ->set('description', 'Categoria para equipamentos de uso hospitalar.')
        ->call('update')
        ->assertHasNoErrors();

    $productType->refresh();

    expect($productType->title)->toBe('Equipamento Hospitalar')
        ->and($productType->description)->toBe('Categoria para equipamentos de uso hospitalar.');
});

