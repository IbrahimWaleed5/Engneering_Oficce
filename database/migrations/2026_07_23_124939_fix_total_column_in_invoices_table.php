<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('invoices', 'total')) {
            DB::statement(
                'ALTER TABLE invoices
                 MODIFY total DECIMAL(10,2)
                 NULL DEFAULT 0'
            );

            DB::statement(
                'UPDATE invoices
                 SET total = amount
                 WHERE (total IS NULL OR total = 0)
                 AND amount IS NOT NULL'
            );
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('invoices', 'total')) {
            DB::statement(
                'ALTER TABLE invoices
                 MODIFY total DECIMAL(10,2)
                 NOT NULL'
            );
        }
    }
};
