<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_audit_campaign_items', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('asset_audit_campaign_id')->constrained('asset_audit_campaigns')->cascadeOnDelete();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->string('status')->default('PENDING');
            $table->timestamp('audited_at')->nullable();
            $table->foreignId('audited_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('photo_path')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('expected_unit_id')->nullable();
            $table->unsignedBigInteger('expected_sector_id')->nullable();
            $table->string('observed_unit')->nullable();
            $table->string('observed_sector')->nullable();
            $table->timestamps();

            $table->unique(['asset_audit_campaign_id', 'asset_id'], 'audit_campaign_item_asset_unique');
            $table->index(['status', 'audited_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_audit_campaign_items');
    }
};

