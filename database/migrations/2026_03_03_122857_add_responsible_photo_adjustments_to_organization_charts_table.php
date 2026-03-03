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
        Schema::table('organization_charts', function (Blueprint $table) {
            $table->unsignedSmallInteger('responsible_photo_x')->default(50);
            $table->unsignedSmallInteger('responsible_photo_y')->default(50);
            $table->unsignedSmallInteger('responsible_photo_zoom')->default(100);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organization_charts', function (Blueprint $table) {
            $table->dropColumn([
                'responsible_photo_x',
                'responsible_photo_y',
                'responsible_photo_zoom',
            ]);
        });
    }
};
