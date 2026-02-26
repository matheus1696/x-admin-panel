<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Task\Task;
use App\Services\Task\TaskService;

$task = Task::query()->first();
if (!$task) {
    echo "no tasks\n";
    exit;
}
$hub = $task->taskHub;
$columns = app(TaskService::class)->kanban($hub->uuid);
foreach ($columns as $col) {
    echo "status_id={$col['status_id']} title={$col['title']} tasks=".$col['tasks']->count()."\n";
}
