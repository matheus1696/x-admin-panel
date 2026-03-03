<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_hub_organization', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_hub_id')->constrained('task_hubs')->cascadeOnDelete();
            $table->foreignId('organization_chart_id')->constrained('organization_charts')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['task_hub_id', 'organization_chart_id'], 'task_hub_organization_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_hub_organization');
    }
};

