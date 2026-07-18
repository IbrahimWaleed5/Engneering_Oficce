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
        $table->foreignId('engineer_id')
            ->nullable()
            ->constrained('users')
            ->nullOnDelete();
    });
}

public function down(): void
{
    Schema::table('consultations', function (Blueprint $table) {
        $table->dropForeign(['engineer_id']);
        $table->dropColumn('engineer_id');
    });
}
};
