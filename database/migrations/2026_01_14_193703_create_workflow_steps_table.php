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
        Schema::create('workflow_steps', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('workflow_id')->constrained('workflows')->cascadeOnDelete();

            $table->foreignId('organization_id')->nullable()->constrained('organization_charts')->cascadeOnDelete();
            $table->string('title');
            $table->string('filter');
            $table->integer('step_order');
            $table->integer('deadline_days');

            $table->boolean('required')->default(true);
            $table->boolean('allow_parallel')->default(false);
            $table->string('step_type')->default('manual');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_stages');
    }
};
