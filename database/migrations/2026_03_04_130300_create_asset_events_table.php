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
        Schema::create('asset_events', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->string('type');
            $table->string('from_state')->nullable();
            $table->string('to_state')->nullable();
            $table->foreignId('from_unit_id')->nullable()->constrained('establishments')->nullOnDelete();
            $table->foreignId('to_unit_id')->nullable()->constrained('establishments')->nullOnDelete();
            $table->foreignId('from_sector_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('to_sector_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->index(['asset_id', 'created_at']);
            $table->index(['type', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_events');
    }
};
