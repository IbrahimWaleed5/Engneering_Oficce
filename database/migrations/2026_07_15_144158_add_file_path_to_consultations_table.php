<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('consultations', function (Blueprint $table) {

        $table->string('customer_file')->nullable();

        $table->string('engineer_file')->nullable();

    });
}

public function down(): void
{
    Schema::table('consultations', function (Blueprint $table) {

        $table->dropColumn([
            'customer_file',
            'engineer_file'
        ]);

    });
}
};
