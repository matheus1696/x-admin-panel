<?php

use App\Models\Process\ProcessStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('processes')
            ->whereIn('status', ['OPEN', 'ON_HOLD'])
            ->update([
                'status' => ProcessStatus::IN_PROGRESS,
            ]);

        DB::table('processes')
            ->where('status', ProcessStatus::IN_PROGRESS)
            ->whereNull('started_at')
            ->update([
                'started_at' => DB::raw('created_at'),
            ]);
    }

    public function down(): void
    {
        // No safe reverse mapping for legacy statuses after normalization.
    }
};
