<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'consultations',
            function (Blueprint $table) {
                $table->dateTime('started_at')
                    ->nullable();

                $table->dateTime(
                    'expected_delivery_at'
                )->nullable();

                $table->dateTime('delivered_at')
                    ->nullable();
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'consultations',
            function (Blueprint $table) {
                $table->dropColumn([
                    'started_at',
                    'expected_delivery_at',
                    'delivered_at',
                ]);
            }
        );
    }
};
