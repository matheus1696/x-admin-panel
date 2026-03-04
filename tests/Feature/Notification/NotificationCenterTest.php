<?php

use App\Models\Administration\User\User;
use App\Services\Notification\NotificationService;

test('users can view their notification center', function () {
    $user = User::factory()->create();

    app(NotificationService::class)->send(
        $user,
        'Nova tarefa',
        'Uma nova tarefa foi atribuida a voce.',
        [
            'url' => route('dashboard'),
            'icon' => 'fa-solid fa-list-check',
        ]
    );

    $response = $this->actingAs($user)->get(route('notifications.index'));

    $response->assertOk();
    $response->assertSee('Nova tarefa');
    $response->assertSee('Uma nova tarefa foi atribuida a voce.');
});

test('users can mark a notification as read', function () {
    $user = User::factory()->create();

    app(NotificationService::class)->send(
        $user,
        'Aviso interno',
        'Leia esta mensagem.'
    );

    $notification = $user->notifications()->firstOrFail();

    $this->actingAs($user)
        ->post(route('notifications.read', $notification->id))
        ->assertRedirect();

    expect($notification->fresh()->read_at)->not->toBeNull();
});

test('dashboard shows recent notifications in the notification card', function () {
    $user = User::factory()->create();

    app(NotificationService::class)->send(
        $user,
        'Atualizacao do sistema',
        'O sistema registrou uma nova mensagem interna.'
    );

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('Atualizacao do sistema');
});
