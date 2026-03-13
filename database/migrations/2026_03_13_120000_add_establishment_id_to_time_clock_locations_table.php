<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('time_clock_locations', function (Blueprint $table) {
            $table->foreignId('establishment_id')
                ->nullable()
                ->after('name')
                ->constrained('establishments')
                ->nullOnDelete();

            $table->index(['establishment_id', 'active']);
        });
    }

    public function down(): void
    {
        Schema::table('time_clock_locations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('establishment_id');
        });
    }
};
