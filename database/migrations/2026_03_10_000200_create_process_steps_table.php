<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('process_steps', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('process_id')->constrained('processes')->cascadeOnDelete();
            $table->unsignedInteger('step_order');
            $table->string('title');
            $table->foreignId('organization_id')->nullable()->constrained('organization_charts')->nullOnDelete();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('deadline_days')->nullable();
            $table->boolean('required')->default(true);
            $table->boolean('is_current')->default(false);
            $table->string('status')->default('PENDING');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['process_id', 'step_order']);
            $table->index(['process_id', 'is_current']);
            $table->index(['process_id', 'owner_id']);
            $table->index(['process_id', 'status']);
            $table->index(['process_id', 'started_at']);
        });

        $this->backfillExistingProcesses();
    }

    public function down(): void
    {
        Schema::dropIfExists('process_steps');
    }

    private function backfillExistingProcesses(): void
    {
        $processes = DB::table('processes')
            ->whereNotNull('workflow_id')
            ->get(['id', 'workflow_id', 'organization_id', 'owner_id']);

        foreach ($processes as $process) {
            $workflowSteps = DB::table('workflow_steps')
                ->where('workflow_id', $process->workflow_id)
                ->orderBy('step_order')
                ->orderBy('id')
                ->get(['id', 'step_order', 'title', 'organization_id', 'deadline_days', 'required']);

            if ($workflowSteps->isEmpty()) {
                continue;
            }

            $currentStepOrder = $workflowSteps
                ->firstWhere('organization_id', $process->organization_id)
                ?->step_order ?? $workflowSteps->first()->step_order;

            $now = now();
            $rows = [];

            foreach ($workflowSteps as $step) {
                $rows[] = [
                    'uuid' => (string) Str::uuid(),
                    'process_id' => $process->id,
                    'step_order' => (int) $step->step_order,
                    'title' => $step->title,
                    'organization_id' => $step->organization_id,
                    'owner_id' => (int) $step->step_order === (int) $currentStepOrder ? $process->owner_id : null,
                    'deadline_days' => $step->deadline_days,
                    'required' => (bool) $step->required,
                    'is_current' => (int) $step->step_order === (int) $currentStepOrder,
                    'status' => (int) $step->step_order < (int) $currentStepOrder
                        ? 'COMPLETED'
                        : ((int) $step->step_order === (int) $currentStepOrder ? 'IN_PROGRESS' : 'PENDING'),
                    'started_at' => (int) $step->step_order === (int) $currentStepOrder ? $now : null,
                    'completed_at' => (int) $step->step_order < (int) $currentStepOrder ? $now : null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('process_steps')->insert($rows);
        }
    }
};
