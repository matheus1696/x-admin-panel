<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $duplicates = DB::table('task_hub_members')
            ->select('task_hub_id', 'user_id', DB::raw('MIN(id) as keep_id'), DB::raw('COUNT(*) as total'))
            ->groupBy('task_hub_id', 'user_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $duplicate) {
            DB::table('task_hub_members')
                ->where('task_hub_id', $duplicate->task_hub_id)
                ->where('user_id', $duplicate->user_id)
                ->where('id', '!=', $duplicate->keep_id)
                ->delete();
        }

        Schema::table('task_hub_members', function (Blueprint $table): void {
            $table->unique(['task_hub_id', 'user_id'], 'task_hub_members_hub_user_unique');
        });
    }

    public function down(): void
    {
        Schema::table('task_hub_members', function (Blueprint $table): void {
            $table->dropUnique('task_hub_members_hub_user_unique');
        });
    }
};
