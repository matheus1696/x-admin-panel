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
        Schema::create('organization_charts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('acronym')->nullable();
            $table->string('title');
            $table->string('filter');
            $table->string('order')->nullable();
            $table->integer('hierarchy');
            $table->string('number_hierarchy')->nullable();
            $table->boolean('status')->default(true);
            $table->string('responsible_photo')->nullable();
            $table->string('responsible_name')->nullable();
            $table->string('responsible_contact')->nullable();
            $table->string('responsible_email')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_charts');
    }
};
