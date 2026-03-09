<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_audit_campaigns', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('title');
            $table->string('status')->default('PLANNED');
            $table->foreignId('unit_id')->nullable()->constrained('establishments')->nullOnDelete();
            $table->foreignId('sector_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('financial_block_id')->nullable()->constrained('financial_blocks')->nullOnDelete();
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->foreignId('created_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('finished_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'start_date', 'due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_audit_campaigns');
    }
};

