<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_release_order_items', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('asset_release_order_id')->constrained('asset_release_orders')->cascadeOnDelete();
            $table->foreignId('asset_id')->constrained('assets')->restrictOnDelete();
            $table->string('item_description');
            $table->string('asset_code');
            $table->string('patrimony_number')->nullable();
            $table->string('invoice_number')->nullable();
            $table->string('financial_block_label')->nullable();
            $table->timestamps();

            $table->unique(['asset_release_order_id', 'asset_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_release_order_items');
    }
};

