<?php

namespace Database\Seeders\Administration\Task;

use App\Models\Administration\Task\TaskPriority;
use Illuminate\Database\Seeder;

class TaskPrioritySeeder extends Seeder
{
    public function run(): void
    {
        TaskPriority::insert([
            ['title' => 'Crítico', 'color' => 'bg-red-100 text-red-800 hover:bg-red-200', 'level' => 1, 'is_default' => false],
            ['title' => 'Alta', 'color' => 'bg-orange-100 text-orange-800 hover:bg-orange-200', 'level' => 2, 'is_default' => false],
            ['title' => 'Média', 'color' => 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200', 'level' => 3, 'is_default' => false],
            ['title' => 'Normal', 'color' => 'bg-green-100 text-green-800 hover:bg-green-200', 'level' => 3, 'is_default' => true],
            ['title' => 'Baixa', 'color' => 'bg-blue-100 text-blue-800 hover:bg-blue-200', 'level' => 4, 'is_default' => false],
        ]);
    }
}
