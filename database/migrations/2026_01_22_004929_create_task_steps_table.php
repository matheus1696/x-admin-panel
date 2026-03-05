<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('task_steps', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
            $table->foreignId('task_hub_id')->constrained();
            $table->string('code')->nullable()->unique();
            $table->string('title');
            $table->longText('description')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('organization_id')->nullable()->constrained('organization_charts');
            $table->foreignId('task_category_id')->nullable()->constrained('task_step_categories');
            $table->foreignId('task_priority_id')->nullable()->constrained('task_priorities');
            $table->foreignId('task_status_id')->nullable()->constrained('task_step_statuses');
            $table->unsignedInteger('workflow_step_order')->nullable();
            $table->unsignedInteger('kanban_order')->nullable()->index();
            $table->boolean('is_required')->default(false);
            $table->boolean('allow_parallel')->default(false);
            $table->foreignId('created_user_id')->nullable()->constrained('users');

            $table->timestamp('started_at')->nullable(); //Quando Iniciou a etapa
            $table->timestamp('finished_at')->nullable(); //Quando Finalizou a etapa
            $table->timestamp('deadline_at')->nullable(); //Prazo para concluir a etapa

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_steps');
    }
};
