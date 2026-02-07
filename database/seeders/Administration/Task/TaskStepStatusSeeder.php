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
                'is_default' => true
            ],
            [
                'title' => 'Aguardando Dependência',
                'color' => 'purple',
                'color_code_tailwind' => 'bg-purple-100 text-purple-700 hover:bg-purple-200',
                'is_default' => false
            ],
            [
                'title' => 'Em execução',
                'color' => 'blue',
                'color_code_tailwind' => 'bg-blue-100 text-blue-600 hover:bg-blue-200',
                'is_default' => false
            ],
            [
                'title' => 'Bloqueada',
                'color' => 'red',
                'color_code_tailwind' => 'bg-red-100 text-red-700 hover:bg-red-200',
                'is_default' => false
            ],
            [
                'title' => 'Concluída',
                'color' => 'green',
                'color_code_tailwind' => 'bg-green-100 text-green-700 hover:bg-green-200',
                'is_default' => false
            ],
        ]);
    }
}
