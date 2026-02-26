<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->unsignedInteger('kanban_order')->nullable()->index();
        });

        $tasks = DB::table('tasks')
            ->select('id', 'task_hub_id', 'task_status_id')
            ->orderBy('task_hub_id')
            ->orderBy('task_status_id')
            ->orderBy('created_at')
            ->get();

        $counters = [];
        foreach ($tasks as $task) {
            $statusKey = $task->task_status_id ?? 0;
            $key = $task->task_hub_id.':'.$statusKey;
            $counters[$key] = ($counters[$key] ?? 0) + 1;

            DB::table('tasks')
                ->where('id', $task->id)
                ->update(['kanban_order' => $counters[$key]]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('kanban_order');
        });
    }
};
