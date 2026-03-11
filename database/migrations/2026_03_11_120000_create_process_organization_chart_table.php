<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('process_organization_chart', function (Blueprint $table) {
            $table->id();
            $table->foreignId('process_id')->constrained('processes')->cascadeOnDelete();
            $table->foreignId('organization_chart_id')->constrained('organization_charts')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['process_id', 'organization_chart_id']);
            $table->index(['organization_chart_id', 'process_id']);
        });

        $this->backfillProcessOrganizations();
    }

    public function down(): void
    {
        Schema::dropIfExists('process_organization_chart');
    }

    private function backfillProcessOrganizations(): void
    {
        $now = now();

        $stepRows = DB::table('process_steps')
            ->whereNotNull('organization_id')
            ->get(['process_id', 'organization_id'])
            ->map(fn (object $row): array => [
                'process_id' => (int) $row->process_id,
                'organization_chart_id' => (int) $row->organization_id,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

        $processRows = DB::table('processes')
            ->whereNotNull('organization_id')
            ->get(['id', 'organization_id'])
            ->map(fn (object $row): array => [
                'process_id' => (int) $row->id,
                'organization_chart_id' => (int) $row->organization_id,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

        $rows = $stepRows
            ->merge($processRows)
            ->unique(fn (array $row): string => $row['process_id'].'-'.$row['organization_chart_id'])
            ->values()
            ->all();

        if ($rows !== []) {
            DB::table('process_organization_chart')->insert($rows);
        }
    }
};
