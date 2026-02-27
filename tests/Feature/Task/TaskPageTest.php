<?php

use App\Livewire\Task\TaskPage;
use App\Models\Administration\Task\TaskStatus;
use App\Models\Administration\User\User;
use App\Models\Task\Task;
use App\Models\Task\TaskActivity;
use App\Models\Task\TaskHub;
use Livewire\Livewire;

function createTaskHubForTaskPage(User $owner, string $title, string $acronym): TaskHub
{
    return TaskHub::create([
        'title' => $title,
        'acronym' => $acronym,
        'owner_id' => $owner->id,
    ]);
}

test('requires completion comment when moving to done', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $hub = createTaskHubForTaskPage($user, 'Hub Kanban UI', 'HUBU');

    TaskStatus::create(['title' => 'Rascunho']);
    $inProgress = TaskStatus::create(['title' => 'Em andamento']);
    $done = TaskStatus::create(['title' => 'Concluído']);
    TaskStatus::create(['title' => 'Cancelado']);

    $task = Task::create([
        'task_hub_id' => $hub->id,
        'title' => 'Task UI',
        'task_status_id' => $inProgress->id,
        'kanban_order' => 1,
    ]);

    Livewire::test(TaskPage::class, ['uuid' => $hub->uuid])
        ->call('requestKanbanMove', $task->id, $inProgress->id, $done->id, [], [$task->id])
        ->assertSet('pendingKanbanReasonType', 'completion')
        ->set('kanbanReason', 'Finalizada')
        ->set('kanbanCompletionComment', 'Entrega aprovada')
        ->call('confirmKanbanMove');

    expect(TaskActivity::where('task_id', $task->id)->where('type', 'comment')->count())->toBe(1);
});
