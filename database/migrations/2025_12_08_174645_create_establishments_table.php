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
        Schema::create('establishments', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable()->unique();
            $table->string('title')->unique();
            $table->string('surname')->nullable()->unique();
            $table->string('filter');
            $table->string('address');
            $table->string('number');
            $table->string('district');
            $table->unsignedBigInteger('city_id');
            $table->unsignedBigInteger('state_id');
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->unsignedBigInteger('type_establishment_id');
            $table->unsignedBigInteger('financial_block_id');
            $table->text('description')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->foreign('city_id')->references('id')->on('region_cities');
            $table->foreign('state_id')->references('id')->on('region_states');
            $table->foreign('type_establishment_id')->references('id')->on('establishment_types');
            $table->foreign('financial_block_id')->references('id')->on('financial_blocks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('establishments');
    }
};
