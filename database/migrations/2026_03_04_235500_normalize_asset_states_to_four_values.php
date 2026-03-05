<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('assets')
            ->where('state', 'RELEASED')
            ->update(['state' => 'IN_USE']);

        DB::table('assets')
            ->where('state', 'RETURNED_TO_PATRIMONY')
            ->update(['state' => 'IN_STOCK']);

        DB::table('asset_events')
            ->where('from_state', 'RELEASED')
            ->update(['from_state' => 'IN_USE']);

        DB::table('asset_events')
            ->where('to_state', 'RELEASED')
            ->update(['to_state' => 'IN_USE']);

        DB::table('asset_events')
            ->where('from_state', 'RETURNED_TO_PATRIMONY')
            ->update(['from_state' => 'IN_STOCK']);

        DB::table('asset_events')
            ->where('to_state', 'RETURNED_TO_PATRIMONY')
            ->update(['to_state' => 'IN_STOCK']);
    }

    public function down(): void
    {
        // No-op: rollback cannot safely infer which records were legacy values.
    }
};
