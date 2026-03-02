<?php

namespace Database\Seeders\Administration\Task;

use App\Models\Administration\Task\TaskStepStatus;
use Illuminate\Database\Seeder;

class TaskStepStatusSeeder extends Seeder
{
    public function run(): void
    {
        TaskStepStatus::insert([
            [
                'title' => 'Pendente',
                'color' => 'gray',
                'color_code_tailwind' => 'bg-gray-100 text-gray-600 hover:bg-gray-200',
                'is_default' => true,
                'is_active' => true,
            ],
            [
                'title' => 'Em andamento',
                'color' => 'blue',
                'color_code_tailwind' => 'bg-blue-100 text-blue-600 hover:bg-blue-200',
                'is_default' => false,
                'is_active' => true,
            ],
            [
                'title' => 'Pausado',
                'color' => 'yellow',
                'color_code_tailwind' => 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200',
                'is_default' => false,
                'is_active' => true,
            ],
            [
                'title' => 'Concluída',
                'color' => 'green',
                'color_code_tailwind' => 'bg-green-100 text-green-700 hover:bg-green-200',
                'is_default' => false,
                'is_active' => false,
            ],
            [
                'title' => 'Cancelada',
                'color' => 'red',
                'color_code_tailwind' => 'bg-red-100 text-red-700 hover:bg-red-200',
                'is_default' => false,
                'is_active' => true,
            ],
        ]);
    }
}
