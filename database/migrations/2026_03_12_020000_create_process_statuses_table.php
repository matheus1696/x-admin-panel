<?php

use App\Models\Process\ProcessStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('process_statuses', function (Blueprint $table): void {
            $table->id();
            $table->string('code')->unique();
            $table->string('label');
            $table->string('badge_class');
            $table->string('chart_color');
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        DB::table('process_statuses')->insert(
            collect(ProcessStatus::defaults())
                ->map(fn (array $item): array => array_merge($item, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]))
                ->all()
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('process_statuses');
    }
};

