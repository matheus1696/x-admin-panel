<?php

use App\Enums\TimeClock\TimeClockEntryStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('time_clock_entries', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('occurred_at');
            $table->string('photo_path')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->float('accuracy')->nullable();
            $table->json('device_meta')->nullable();
            $table->string('status')->default(TimeClockEntryStatus::OK->value);
            $table->foreignId('location_id')->nullable()->constrained('time_clock_locations')->nullOnDelete();
            $table->timestamps();

            $table->index(['user_id', 'occurred_at']);
            $table->index(['occurred_at']);
            $table->index(['status', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('time_clock_entries');
    }
};
