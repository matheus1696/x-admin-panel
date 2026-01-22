<?php

namespace Database\Seeders\Organization\Workflow;

use App\Models\Organization\Workflow\WorkflowRunStatus;
use Illuminate\Database\Seeder;

class WorkflowRunStatusSeeder extends Seeder
{
    public function run(): void
    {
        WorkflowRunStatus::insert([
            [
                'title' => 'Rascunho',
                'color' => 'bg-gray-200 text-gray-700 hover:bg-gray-300',
            ],
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
