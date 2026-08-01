<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        /*
         * هذا التعديل خاص بـ MySQL.
         * اختبارات Laravel تستخدم SQLite، لذلك نتجاوزه أثناء الاختبار.
         */
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("
            ALTER TABLE consultations
            MODIFY status VARCHAR(30)
            NOT NULL DEFAULT 'waiting_payment'
        ");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("
            ALTER TABLE consultations
            MODIFY status VARCHAR(30)
            NOT NULL DEFAULT 'pending'
        ");
    }
};
