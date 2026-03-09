<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_release_orders', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('code')->unique();
            $table->string('status')->default('RELEASED');
            $table->foreignId('to_unit_id')->constrained('establishments')->restrictOnDelete();
            $table->foreignId('to_sector_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->string('requester_name');
            $table->string('receiver_name')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedInteger('total_assets')->default(0);
            $table->timestamp('released_at')->nullable();
            $table->foreignId('released_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'released_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_release_orders');
    }
};

