<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('payments', 'consultation_id')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->foreignId('consultation_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('consultations')
                    ->nullOnDelete();
            });
        }

        if (!Schema::hasColumn('payments', 'customer_id')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->foreignId('customer_id')
                    ->nullable()
                    ->after('consultation_id')
                    ->constrained('users')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('payments', 'customer_id')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropForeign(['customer_id']);
                $table->dropColumn('customer_id');
            });
        }

        if (Schema::hasColumn('payments', 'consultation_id')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropForeign(['consultation_id']);
                $table->dropColumn('consultation_id');
            });
        }
    }
};
