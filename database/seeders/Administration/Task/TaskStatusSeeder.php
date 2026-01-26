<?php

namespace Database\Seeders\Administration\Task;

use App\Models\Administration\Task\TaskStatus;
use Illuminate\Database\Seeder;

class TaskStatusSeeder extends Seeder
{
    public function run(): void
    {
        TaskStatus::insert([
            [
                'title' => 'Em andamento',
                'color' => 'bg-blue-100 text-blue-600 hover:bg-blue-200',
            ],
            [
                'title' => 'Pausado',
                'color' => 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200',
            ],
            [
                'title' => 'Concluído',
                'color' => 'bg-green-100 text-green-700 hover:bg-green-200',
            ],
            [
                'title' => 'Cancelado',
                'color' => 'bg-red-100 text-red-700 hover:bg-red-200',
            ],
        ]);
    }
}
