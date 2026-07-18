<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE consultations
            MODIFY status ENUM(
                'waiting_payment',
                'pending',
                'in_progress',
                'completed',
                'cancelled'
            ) NOT NULL DEFAULT 'waiting_payment'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE consultations
            MODIFY status ENUM(
                'pending',
                'in_progress',
                'completed',
                'cancelled'
            ) NOT NULL DEFAULT 'pending'
        ");
    }
};
