<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

auth()->login(App\Models\Administration\User\User::query()->first());
$hub = App\Models\Task\TaskHub::query()->first();
if (!$hub) { echo "no hub\n"; exit; }

echo view('livewire.task.task-page')->render();
