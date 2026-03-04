<?php

use App\Livewire\Task\TaskPage;
use App\Models\Administration\Task\TaskCategory;
use App\Models\Administration\User\User;
use App\Models\Task\TaskHub;
use App\Models\Task\TaskHubMember;
use Livewire\Livewire;

function createTaskHubForCategories(User $owner, string $title, string $acronym): TaskHub
{
    return TaskHub::create([
        'title' => $title,
        'acronym' => $acronym,
        'owner_id' => $owner->id,
    ]);
}

test('owner can manage local task categories from task page', function () {
    $owner = User::factory()->create();

    $this->actingAs($owner);

    $hub = createTaskHubForCategories($owner, 'Hub Categorias', 'HUBC');

    Livewire::test(TaskPage::class, ['uuid' => $hub->uuid])
        ->set('taskHubCategoryTitle', 'Operacional')
        ->set('taskHubCategoryDescription', 'Fluxos internos deste ambiente')
        ->call('storeTaskCategory');

    $category = TaskCategory::query()
        ->where('task_hub_id', $hub->id)
        ->first();

    expect($category)->not->toBeNull();
    expect($category->title)->toBe('Operacional');
    expect((bool) $category->is_active)->toBeTrue();

    Livewire::test(TaskPage::class, ['uuid' => $hub->uuid])
        ->call('editTaskCategory', $category->id)
        ->set('taskHubCategoryTitle', 'Operacional Interna')
        ->set('taskHubCategoryDescription', 'Ajustada para este hub')
        ->call('updateTaskCategory');

    $category->refresh();

    expect($category->title)->toBe('Operacional Interna');
    expect($category->description)->toBe('Ajustada para este hub');

    Livewire::test(TaskPage::class, ['uuid' => $hub->uuid])
        ->call('toggleTaskCategoryStatus', $category->id);

    $category->refresh();

    expect((bool) $category->is_active)->toBeFalse();
});

test('shared member cannot manage local task categories', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();

    $hub = createTaskHubForCategories($owner, 'Hub Restrito', 'HUBR');

    TaskHubMember::create([
        'task_hub_id' => $hub->id,
        'user_id' => $member->id,
    ]);

    $this->actingAs($member);

    Livewire::test(TaskPage::class, ['uuid' => $hub->uuid])
        ->set('taskHubCategoryTitle', 'Não permitida')
        ->call('storeTaskCategory');

    expect(
        TaskCategory::query()
            ->where('task_hub_id', $hub->id)
            ->where('title', 'Não permitida')
            ->exists()
    )->toBeFalse();
});
