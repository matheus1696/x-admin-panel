<?php

use App\Enums\Process\ProcessStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('processes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('code')->nullable()->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('organization_id')->nullable()->constrained('organization_charts')->nullOnDelete();
            $table->foreignId('workflow_id')->nullable()->constrained('workflows')->nullOnDelete();
            $table->foreignId('opened_by')->constrained('users');
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('priority')->default(config('process.default_priority', 'normal'));
            $table->string('status')->default(ProcessStatus::OPEN->value);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['organization_id', 'created_at']);
            $table->index(['opened_by', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('processes');
    }
};
