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
            $table->string('code')->nullable()->unique();
            $table->string('title');
            $table->longText('description')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('organization_id')->nullable()->constrained('organization_charts');
            $table->foreignId('task_category_id')->nullable()->constrained('task_step_categories');
            $table->foreignId('task_priority_id')->nullable()->constrained('task_priorities');
            $table->foreignId('task_status_id')->nullable()->constrained('task_step_statuses');

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
