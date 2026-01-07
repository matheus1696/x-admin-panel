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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('acronym')->nullable();
            $table->string('filter');
            $table->text('description')->nullable();

            $table->foreignId('parent_id')->nullable()->constrained('departments')->nullOnDelete();

            $table->integer('order')->nullable();
            $table->integer('level')->nullable();
            $table->string('path')->nullable();

            $table->boolean('status')->default(true);
            
            $table->string('contact')->nullable();
            $table->string('extension')->nullable();
            $table->string('type_contact')->nullable()->default('Without');
            $table->unsignedBigInteger('establishment_id');
            $table->timestamps();

            $table->foreign('establishment_id')->references('id')->on('establishments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
