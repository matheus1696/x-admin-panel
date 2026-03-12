<?php

use App\Models\Administration\User\User;
use App\Models\Organization\OrganizationChart\OrganizationChart;
use App\Models\Process\Process;
use App\Models\Process\ProcessStep;
use App\Services\Notification\NotificationService;
use Spatie\Permission\Models\Permission;

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

test('dashboard shows process card with accessible processes for the user', function () {
    $user = User::factory()->create();
    $creator = User::factory()->create();

    Permission::findOrCreate('process.view', 'web');
    $user->givePermissionTo('process.view');

    $organization = OrganizationChart::query()->create([
        'title' => 'Setor do Dashboard',
        'filter' => 'setor do dashboard',
        'hierarchy' => 0,
        'number_hierarchy' => 90,
        'order' => '090',
    ]);

    $user->organizations()->attach($organization->id);

    $visibleProcess = Process::query()->create([
        'title' => 'Processo visivel no dashboard',
        'description' => 'Descricao',
        'organization_id' => $organization->id,
        'opened_by' => $creator->id,
        'owner_id' => $creator->id,
        'priority' => 'normal',
        'status' => 'IN_PROGRESS',
        'started_at' => now(),
    ]);
    $visibleProcess->organizations()->sync([$organization->id]);

    ProcessStep::query()->create([
        'process_id' => $visibleProcess->id,
        'step_order' => 1,
        'title' => 'Etapa dashboard',
        'organization_id' => $organization->id,
        'deadline_days' => 2,
        'required' => true,
        'is_current' => true,
        'status' => 'IN_PROGRESS',
        'started_at' => now(),
    ]);

    $hiddenProcess = Process::query()->create([
        'title' => 'Processo oculto no dashboard',
        'description' => 'Descricao',
        'opened_by' => $creator->id,
        'owner_id' => $creator->id,
        'priority' => 'normal',
        'status' => 'IN_PROGRESS',
        'started_at' => now(),
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('Processos que voce faz parte');
    $response->assertSee('Processo visivel no dashboard');
    $response->assertDontSee('Processo oculto no dashboard');
});
