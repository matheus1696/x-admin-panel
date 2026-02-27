<?php

use App\Models\Administration\User\User;
use App\Models\Task\TaskHub;
use App\Models\Task\TaskHubMember;

function createTaskHub(User $owner, string $title, string $acronym): TaskHub
{
    return TaskHub::create([
        'title' => $title,
        'acronym' => $acronym,
        'owner_id' => $owner->id,
    ]);
}

test('users only see task hubs they own or are shared on', function () {
    $owner = User::factory()->create();
    $sharedUser = User::factory()->create();
    $otherOwner = User::factory()->create();

    $ownedHub = createTaskHub($sharedUser, 'Hub A', 'HUBA');
    $sharedHub = createTaskHub($owner, 'Hub B', 'HUBB');
    $privateHub = createTaskHub($otherOwner, 'Hub C', 'HUBC');

    TaskHubMember::create([
        'task_hub_id' => $sharedHub->id,
        'user_id' => $sharedUser->id,
    ]);

    $response = $this->actingAs($sharedUser)->get('/tarefas');

    $response->assertOk();
    $response->assertSee('Hub A');
    $response->assertSee('Hub B');
    $response->assertDontSee('Hub C');
});

test('users cannot access task hubs they do not own or share', function () {
    $user = User::factory()->create();
    $otherOwner = User::factory()->create();

    $privateHub = createTaskHub($otherOwner, 'Hub Privado', 'HUBP');

    $this->actingAs($user)
        ->get('/tarefas/' . $privateHub->uuid)
        ->assertNotFound();
});

test('users can access owned or shared task hubs', function () {
    $owner = User::factory()->create();
    $sharedUser = User::factory()->create();

    $ownedHub = createTaskHub($sharedUser, 'Hub D', 'HUBD');
    $sharedHub = createTaskHub($owner, 'Hub E', 'HUBE');

    TaskHubMember::create([
        'task_hub_id' => $sharedHub->id,
        'user_id' => $sharedUser->id,
    ]);

    $this->actingAs($sharedUser)
        ->get('/tarefas/' . $ownedHub->uuid)
        ->assertOk();

    $this->actingAs($sharedUser)
        ->get('/tarefas/' . $sharedHub->uuid)
        ->assertOk();
});
