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
            if (Schema::hasColumn('organization_charts', 'responsible_photo')) {
                $table->dropColumn('responsible_photo');
            }
            if (Schema::hasColumn('organization_charts', 'responsible_name')) {
                $table->dropColumn('responsible_name');
            }
            if (Schema::hasColumn('organization_charts', 'responsible_contact')) {
                $table->dropColumn('responsible_contact');
            }
            if (Schema::hasColumn('organization_charts', 'responsible_email')) {
                $table->dropColumn('responsible_email');
            }
            if (Schema::hasColumn('organization_charts', 'responsible_photo_x')) {
                $table->dropColumn('responsible_photo_x');
            }
            if (Schema::hasColumn('organization_charts', 'responsible_photo_y')) {
                $table->dropColumn('responsible_photo_y');
            }
            if (Schema::hasColumn('organization_charts', 'responsible_photo_zoom')) {
                $table->dropColumn('responsible_photo_zoom');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organization_charts', function (Blueprint $table) {
            if (! Schema::hasColumn('organization_charts', 'responsible_photo')) {
                $table->string('responsible_photo')->nullable();
            }
            if (! Schema::hasColumn('organization_charts', 'responsible_name')) {
                $table->string('responsible_name')->nullable();
            }
            if (! Schema::hasColumn('organization_charts', 'responsible_contact')) {
                $table->string('responsible_contact')->nullable();
            }
            if (! Schema::hasColumn('organization_charts', 'responsible_email')) {
                $table->string('responsible_email')->nullable();
            }
            if (! Schema::hasColumn('organization_charts', 'responsible_photo_x')) {
                $table->unsignedSmallInteger('responsible_photo_x')->default(50);
            }
            if (! Schema::hasColumn('organization_charts', 'responsible_photo_y')) {
                $table->unsignedSmallInteger('responsible_photo_y')->default(50);
            }
            if (! Schema::hasColumn('organization_charts', 'responsible_photo_zoom')) {
                $table->unsignedSmallInteger('responsible_photo_zoom')->default(100);
            }
        });
    }
};
