<?php

namespace Database\Seeders\Administration\Task;

use App\Models\Administration\Task\TaskPriority;
use Illuminate\Database\Seeder;

class TaskPrioritySeeder extends Seeder
{
    public function run(): void
    {
        TaskPriority::insert([
            ['title' => 'Crítico', 'color' => 'red', 'color_code_tailwind' => 'bg-red-100 text-red-800 hover:bg-red-200', 'level' => 1, 'is_default' => false],
            ['title' => 'Alta', 'color' => 'orange', 'color_code_tailwind' => 'bg-orange-100 text-orange-800 hover:bg-orange-200', 'level' => 2, 'is_default' => false],
            ['title' => 'Média', 'color' => 'yellow', 'color_code_tailwind' => 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200', 'level' => 3, 'is_default' => false],
            ['title' => 'Normal', 'color' => 'green', 'color_code_tailwind' => 'bg-green-100 text-green-800 hover:bg-green-200', 'level' => 3, 'is_default' => true],
            ['title' => 'Baixa', 'color' => 'blue', 'color_code_tailwind' => 'bg-blue-100 text-blue-800 hover:bg-blue-200', 'level' => 4, 'is_default' => false],
        ]);
    }
}
