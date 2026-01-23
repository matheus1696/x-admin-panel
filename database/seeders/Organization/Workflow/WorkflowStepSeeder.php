<?php

namespace Database\Seeders\Organization\Workflow;

use App\Models\Organization\Workflow\WorkflowStep;
use Illuminate\Database\Seeder;

class WorkflowStepSeeder extends Seeder
{
    public function run(): void
    {
        WorkflowStep::create([
            'workflow_id' => 1,
            'title' => 'Elaboração do documento de formalização da demanda - DFD',
            'step_order' => 1,
            'deadline_days' => 2,
        ]);

        WorkflowStep::create([
            'workflow_id' => 1,
            'title' => 'Elaboração de cotação',
            'step_order' => 2,
            'deadline_days' => 30,
        ]);

        WorkflowStep::create([
            'workflow_id' => 1,
            'title' => 'Elaboração de estudo técnico',
            'step_order' => 3,
            'deadline_days' => 3,
        ]);

        WorkflowStep::create([
            'workflow_id' => 1,
            'title' => 'Elaboração do termo de referência',
            'step_order' => 4,
            'deadline_days' => 3,
        ]);

        WorkflowStep::create([
            'workflow_id' => 1,
            'title' => 'Autorização do(a) ordenador(a) de despesa',
            'step_order' => 5,
            'deadline_days' => 2,
        ]);

        WorkflowStep::create([
            'workflow_id' => 1,
            'title' => 'Cadastramento no sistema SIAFIC',
            'step_order' => 6,
            'deadline_days' => 2,
        ]);

        WorkflowStep::create([
            'workflow_id' => 1,
            'title' => 'Elaboração de edital',
            'step_order' => 7,
            'deadline_days' => 3,
        ]);

        WorkflowStep::create([
            'workflow_id' => 1,
            'title' => 'Emissão de parecer jurídico - fase interna',
            'step_order' => 8,
            'deadline_days' => 3,
        ]);

        WorkflowStep::create([
            'workflow_id' => 1,
            'title' => 'Publicação do edital',
            'step_order' => 9,
            'deadline_days' => 5,
        ]);

        WorkflowStep::create([
            'workflow_id' => 1,
            'title' => 'Fase de jugamento do processo: Habilitação jurídica e técnica',
            'step_order' => 10,
            'deadline_days' => 30,
        ]);

        WorkflowStep::create([
            'workflow_id' => 1,
            'title' => 'Emissão de parecer jurídico - fase externa',
            'step_order' => 11,
            'deadline_days' => 3,
        ]);

        WorkflowStep::create([
            'workflow_id' => 1,
            'title' => 'Homologação da fase externa do processo',
            'step_order' => 12,
            'deadline_days' => 3,
        ]);

        WorkflowStep::create([
            'workflow_id' => 1,
            'title' => 'Publicação da homologação - fase externa do processo',
            'step_order' => 13,
            'deadline_days' => 3,
        ]);

        WorkflowStep::create([
            'workflow_id' => 1,
            'title' => 'Elaboração/Emissão de atas de registro de preços / contratos',
            'step_order' => 14,
            'deadline_days' => 3,
        ]);

        WorkflowStep::create([
            'workflow_id' => 1,
            'title' => 'Emissão de ordens de fornecimento / serviço',
            'step_order' => 15,
            'deadline_days' => 2,
        ]);

        WorkflowStep::create([
            'workflow_id' => 1,
            'title' => 'Etapa Final',
            'step_order' => 16,
            'deadline_days' => 1,
        ]);
    }
}
