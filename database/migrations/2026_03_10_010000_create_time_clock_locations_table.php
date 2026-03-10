<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('time_clock_locations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->unsignedInteger('radius_meters')->default(config('time_clock.default_location_radius_meters', 150));
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index(['active', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('time_clock_locations');
    }
};
