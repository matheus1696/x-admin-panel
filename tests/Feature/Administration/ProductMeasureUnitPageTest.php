<?php

use App\Livewire\Administration\Product\ProductMeasureUnitPage;
use App\Models\Administration\Product\ProductMeasureUnit;
use App\Models\Administration\User\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;

function createProductMeasureUnitManagerUser(): User
{
    $user = User::factory()->create();

    Permission::findOrCreate('administration.manage.product-measure-units', 'web');
    $user->givePermissionTo('administration.manage.product-measure-units');

    return $user;
}

test('product measure unit page requires permission and renders for authorized users', function () {
    $authorized = createProductMeasureUnitManagerUser();
    $unauthorized = User::factory()->create();

    $this->actingAs($authorized)
        ->get(route('administration.manage.product-measure-units'))
        ->assertOk();

    $this->actingAs($unauthorized)
        ->get(route('administration.manage.product-measure-units'))
        ->assertRedirect(route('dashboard'));
});

test('product measure unit page creates and updates through livewire', function () {
    $user = createProductMeasureUnitManagerUser();
    $this->actingAs($user);

    Livewire::test(ProductMeasureUnitPage::class)
        ->call('create')
        ->set('acronym', 'PCT/6')
        ->set('title', 'Pacote com 6')
        ->set('base_quantity', 6)
        ->call('store')
        ->assertHasNoErrors();

    $measureUnit = ProductMeasureUnit::query()->where('acronym', 'PCT/6')->firstOrFail();

    Livewire::test(ProductMeasureUnitPage::class)
        ->call('edit', $measureUnit->id)
        ->set('acronym', 'PCT/12')
        ->set('title', 'Pacote com 12')
        ->set('base_quantity', 12)
        ->call('update')
        ->assertHasNoErrors();

    $measureUnit->refresh();

    expect($measureUnit->acronym)->toBe('PCT/12')
        ->and($measureUnit->title)->toBe('Pacote com 12')
        ->and($measureUnit->base_quantity)->toBe(12);
});

