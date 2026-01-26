<?php

namespace Database\Seeders\Administration\Task;

use App\Models\Administration\Task\TaskCategory;
use Illuminate\Database\Seeder;

class TaskCategorySeeder extends Seeder
{
    public function run(): void
    {
        TaskCategory::insert([
            ['title' => 'Processo Licitatório'],
            ['title' => 'Compra Direta'],
            ['title' => 'Cotação de Equipamento'],
        ]);
    }
}
