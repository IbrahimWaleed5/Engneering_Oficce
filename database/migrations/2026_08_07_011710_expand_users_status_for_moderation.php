<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement(
            "ALTER TABLE users
             MODIFY status VARCHAR(50)
             NOT NULL DEFAULT 'active'"
        );
    }

    public function down(): void
    {
        DB::table('users')
            ->where('status', 'suspended_pending_review')
            ->update([
                'status' => 'suspended',
            ]);

        DB::statement(
            "ALTER TABLE users
             MODIFY status ENUM(
                'active',
                'inactive',
                'suspended'
             )
             NOT NULL DEFAULT 'active'"
        );
    }
};
