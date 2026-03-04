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
        Schema::create('assets', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('invoice_item_id')->nullable()->constrained('asset_invoice_items')->nullOnDelete();
            $table->string('code')->unique();
            $table->string('serial_number')->nullable();
            $table->string('patrimony_number')->nullable();
            $table->string('description');
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('state');
            $table->foreignId('unit_id')->nullable()->constrained('establishments')->nullOnDelete();
            $table->foreignId('sector_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('created_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('acquired_date')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['state', 'unit_id', 'sector_id']);
            $table->index('invoice_item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
