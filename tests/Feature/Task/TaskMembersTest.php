<?php

use App\Livewire\Task\TaskPage;
use App\Models\Administration\User\User;
use App\Models\Task\TaskHub;
use App\Models\Task\TaskHubMember;
use Livewire\Livewire;

function createTaskHubForMembers(User $owner, string $title, string $acronym): TaskHub
{
    return TaskHub::create([
        'title' => $title,
        'acronym' => $acronym,
        'owner_id' => $owner->id,
    ]);
}

test('owner can add and remove task hub members from task page', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();

    $this->actingAs($owner);

    $hub = createTaskHubForMembers($owner, 'Hub Members', 'HUBM');

    Livewire::test(TaskPage::class, ['uuid' => $hub->uuid])
        ->set('member_user_id', $member->id)
        ->call('addMember');

    $membership = TaskHubMember::where('task_hub_id', $hub->id)
        ->where('user_id', $member->id)
        ->first();

    expect($membership)->not->toBeNull();

    Livewire::test(TaskPage::class, ['uuid' => $hub->uuid])
        ->call('removeMember', $membership->id);

    expect(TaskHubMember::whereKey($membership->id)->exists())->toBeFalse();
});
