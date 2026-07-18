<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consultations', function (Blueprint $table) {

            $table->enum('payment_status', [
                'unpaid',
                'pending',
                'paid',
                'refunded',
            ])->default('unpaid');

        });
    }

    public function down(): void
    {
        Schema::table('consultations', function (Blueprint $table) {

            $table->dropColumn('payment_status');

        });
    }
};
