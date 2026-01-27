<?php

namespace Database\Seeders\Administration\Task;

use App\Models\Administration\Task\TaskPriority;
use Illuminate\Database\Seeder;

class TaskPrioritySeeder extends Seeder
{
    public function run(): void
    {
        TaskPriority::insert([
            ['title' => 'Crítico', 'level' => 1, 'is_default' => false],
            ['title' => 'Alta',    'level' => 2, 'is_default' => false],
            ['title' => 'Média',   'level' => 3, 'is_default' => false],
            ['title' => 'Normal',  'level' => 3, 'is_default' => true],
            ['title' => 'Baixa',   'level' => 4, 'is_default' => false],
        ]);
    }
}
