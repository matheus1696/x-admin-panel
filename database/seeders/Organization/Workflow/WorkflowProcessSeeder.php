<?php

namespace Database\Seeders\Organization\Workflow;

use App\Models\Organization\Workflow\Workflow;
use Illuminate\Database\Seeder;

class WorkflowProcessSeeder extends Seeder
{
    public function run(): void
    {
        Workflow::create([
            'title' => 'Processo Licitatório',
            'total_estimated_days' => 98,
        ]);
    }
}
