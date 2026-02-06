<?php

namespace Database\Seeders\Administration\Task;

use App\Models\Administration\Task\TaskStepCategory;
use Illuminate\Database\Seeder;

class TaskStepCategorySeeder extends Seeder
{
    public function run(): void
    {
        TaskStepCategory::insert([
            ['title' => 'Cotação'],
            ['title' => 'Elaboração do Edital'],
            ['title' => 'Publicação do Edital'],
            ['title' => 'Fase de Jugamento do Processo'],
        ]);
    }
}
