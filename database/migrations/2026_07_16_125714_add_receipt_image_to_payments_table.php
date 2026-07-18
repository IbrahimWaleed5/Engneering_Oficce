<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('payments', 'receipt_image')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->string('receipt_image')
                    ->nullable()
                    ->after('payment_method');
            });
        }

        if (!Schema::hasColumn('payments', 'transaction_reference')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->string('transaction_reference')
                    ->nullable()
                    ->after('payment_method');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('payments', 'receipt_image')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropColumn('receipt_image');
            });
        }

        if (Schema::hasColumn('payments', 'transaction_reference')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropColumn('transaction_reference');
            });
        }
    }
};
