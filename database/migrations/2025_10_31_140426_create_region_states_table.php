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
        Schema::create('region_states', function (Blueprint $table) {
            $table->id();
            $table->string('acronym')->unique();
            $table->string('state');
            $table->string('filter');
            $table->string('code_uf');
            $table->string('code_ddd')->unique()->nullable();
            $table->boolean('status')->default(true);
            $table->unsignedBigInteger('country_id');
            $table->timestamps();

            $table->foreign('country_id')->references('id')->on('region_countries');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('region_states');
    }
};
