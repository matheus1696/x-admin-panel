<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('code')->nullable()->unique();
            $table->string('sku', 80)->nullable()->unique();
            $table->string('title')->unique();
            $table->string('filter');
            $table->string('nature', 20)->default('ASSET');
            $table->foreignId('product_department_id')
                ->nullable()
                ->constrained('product_departments')
                ->nullOnDelete();
            $table->foreignId('product_type_id')
                ->nullable()
                ->constrained('product_types')
                ->nullOnDelete();
            $table->foreignId('default_measure_unit_id')
                ->nullable()
                ->constrained('product_measure_units')
                ->nullOnDelete();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
