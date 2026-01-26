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
                'color' => 'bg-gray-100 text-gray-600 hover:bg-gray-200',
            ],
            [
                'title' => 'Aguardando Dependência',
                'color' => 'bg-purple-100 text-purple-700 hover:bg-purple-200',
            ],
            [
                'title' => 'Em execução',
                'color' => 'bg-blue-100 text-blue-600 hover:bg-blue-200',
            ],
            [
                'title' => 'Bloqueada',
                'color' => 'bg-red-100 text-red-700 hover:bg-red-200',
            ],
            [
                'title' => 'Concluída',
                'color' => 'bg-green-100 text-green-700 hover:bg-green-200',
            ],
        ]);
    }
}
