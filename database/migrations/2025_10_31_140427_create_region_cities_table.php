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
        Schema::create('region_cities', function (Blueprint $table) {
            $table->id();
            $table->string('code_ibge');
            $table->string('city');
            $table->string('filter');
            $table->string('code_cep')->nullable();
            $table->boolean('status')->default(false);
            $table->unsignedBigInteger('state_id');
            $table->timestamps();

            $table->foreign('state_id')->references('id')->on('region_states');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('region_cities');
    }
};
