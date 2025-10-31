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
        Schema::create('region_countries', function (Blueprint $table) {
            $table->id();
            $table->string('acronym_2')->unique();
            $table->string('acronym_3')->unique();
            $table->string('country');
            $table->string('filter');
            $table->string('country_ing');
            $table->string('filter_country_ing');
            $table->string('code_iso')->unique();
            $table->string('code_ddi')->unique()->nullable();
            $table->boolean('status')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('region_countries');
    }
};
