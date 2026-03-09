<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_audit_issues', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('asset_audit_campaign_id')->constrained('asset_audit_campaigns')->cascadeOnDelete();
            $table->foreignId('asset_audit_campaign_item_id')->constrained('asset_audit_campaign_items')->cascadeOnDelete();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->string('issue_type');
            $table->string('status')->default('OPEN');
            $table->text('notes')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('resolved_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'issue_type', 'opened_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_audit_issues');
    }
};

