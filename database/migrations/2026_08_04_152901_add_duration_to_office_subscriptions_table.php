<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('office_subscriptions', function (Blueprint $table) {
            $table
                ->unsignedInteger('duration_value')
                ->default(1)
                ->after('currency');

            $table
                ->string('duration_unit', 20)
                ->default('month')
                ->after('duration_value');
        });
    }

    public function down(): void
    {
        Schema::table('office_subscriptions', function (Blueprint $table) {
            $table->dropColumn([
                'duration_value',
                'duration_unit',
            ]);
        });
    }
};
