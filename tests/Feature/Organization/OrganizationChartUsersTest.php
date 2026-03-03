<?php

use App\Livewire\Organization\OrganizationChart\OrganizationChartConfigPage;
use App\Models\Administration\User\User;
use App\Models\Organization\OrganizationChart\OrganizationChart;
use App\Services\Organization\OrganizationChart\OrganizationChartService;
use Livewire\Livewire;

test('can open users modal and load existing associations', function () {
    $organization = OrganizationChart::create([
        'acronym' => 'TI',
        'title' => 'Tecnologia',
        'filter' => 'tecnologia',
        'hierarchy' => 0,
        'order' => '0TI',
        'number_hierarchy' => 1,
        'is_active' => true,
    ]);

    $userA = User::factory()->create();
    $userB = User::factory()->create();

    $organization->users()->attach([$userA->id, $userB->id]);

    $component = Livewire::test(OrganizationChartConfigPage::class)
        ->call('openUsers', $organization->id)
        ->assertSet('usersOrganizationId', $organization->id);

    $ids = collect($component->get('organizationUserIds'))
        ->sort()
        ->values()
        ->all();

    expect($ids)->toBe(collect([$userA->id, $userB->id])->sort()->values()->all());
});

test('can save user associations for a sector', function () {
    $organization = OrganizationChart::create([
        'acronym' => 'RH',
        'title' => 'Recursos Humanos',
        'filter' => 'recursos humanos',
        'hierarchy' => 0,
        'order' => '0RH',
        'number_hierarchy' => 1,
        'is_active' => true,
    ]);

    $user = User::factory()->create();

    Livewire::test(OrganizationChartConfigPage::class)
        ->set('usersOrganizationId', $organization->id)
        ->set('organizationUserIds', [$user->id])
        ->call('saveUsers');

    $organization->refresh();

    expect($organization->users()->pluck('users.id')->all())
        ->toBe([$user->id]);
});

test('stores responsible user for a sector when associated', function () {
    $user = User::factory()->create();

    $organization = OrganizationChart::create([
        'acronym' => 'TI',
        'title' => 'Tecnologia',
        'filter' => 'tecnologia',
        'hierarchy' => 0,
        'order' => '0TI',
        'number_hierarchy' => 1,
        'is_active' => true,
    ]);

    $organization->users()->attach($user->id);

    Livewire::test(OrganizationChartConfigPage::class)
        ->set('chartId', $organization->id)
        ->set('title', $organization->title)
        ->set('acronym', $organization->acronym)
        ->set('hierarchy', $organization->hierarchy)
        ->set('responsible_user_id', $user->id)
        ->call('update')
        ->assertHasNoErrors();

    $organization->refresh();

    expect($organization->responsible_user_id)->toBe($user->id);
});

test('requires responsible user to be associated with the sector', function () {
    $organization = OrganizationChart::create([
        'acronym' => 'ADM',
        'title' => 'Administrativo',
        'filter' => 'administrativo',
        'hierarchy' => 0,
        'order' => '0ADM',
        'number_hierarchy' => 1,
        'is_active' => true,
    ]);

    $user = User::factory()->create();

    Livewire::test(OrganizationChartConfigPage::class)
        ->set('chartId', $organization->id)
        ->set('title', $organization->title)
        ->set('acronym', $organization->acronym)
        ->set('hierarchy', $organization->hierarchy)
        ->set('responsible_user_id', $user->id)
        ->call('update')
        ->assertHasErrors(['responsible_user_id']);
});

test('filters sectors by responsible user', function () {
    $userA = User::factory()->create();
    $userB = User::factory()->create();

    OrganizationChart::create([
        'acronym' => 'TI',
        'title' => 'Tecnologia',
        'filter' => 'tecnologia',
        'hierarchy' => 0,
        'order' => '0TI',
        'number_hierarchy' => 1,
        'is_active' => true,
        'responsible_user_id' => $userA->id,
    ]);

    OrganizationChart::create([
        'acronym' => 'ADM',
        'title' => 'Administrativo',
        'filter' => 'administrativo',
        'hierarchy' => 0,
        'order' => '0ADM',
        'number_hierarchy' => 1,
        'is_active' => true,
        'responsible_user_id' => $userB->id,
    ]);

    $service = app(OrganizationChartService::class);

    $filters = [
        'acronym' => '',
        'filter' => '',
        'status' => 'all',
        'responsible_user_id' => (string) $userA->id,
    ];

    $result = $service->index($filters);

    expect($result)->toHaveCount(1)
        ->and($result->first()->responsible_user_id)->toBe($userA->id);
});
