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
        Schema::create('workflow_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('process_workflow_id')->constrained()->cascadeOnDelete();

            $table->string('title'); // Pesquisa de Preço
            $table->string('filter');
            $table->integer('order'); // ordem no fluxo
            $table->integer('deadline_days'); // prazo em dias
            $table->boolean('required')->default(true); // obrigatório
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
