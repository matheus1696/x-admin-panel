<?php

namespace Database\Seeders\Administration\Task;

use App\Models\Administration\Task\TaskPriority;
use Illuminate\Database\Seeder;

class TaskPrioritySeeder extends Seeder
{
    public function run(): void
    {
        TaskPriority::insert([
            ['title' => 'Critical', 'level' => 1],
            ['title' => 'High',     'level' => 2],
            ['title' => 'Medium',   'level' => 3],
            ['title' => 'Low',      'level' => 4],
        ]);
    }
}
