<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (! Schema::hasColumn('invoices', 'payment_id')) {
                $table->foreignId('payment_id')
                    ->nullable()
                    ->after('invoice_number')
                    ->constrained('payments')
                    ->cascadeOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'payment_id')) {
                $table->dropForeign(['payment_id']);
                $table->dropColumn('payment_id');
            }
        });
    }
};
