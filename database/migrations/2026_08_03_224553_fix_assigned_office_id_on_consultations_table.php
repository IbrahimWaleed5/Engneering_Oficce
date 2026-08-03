<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('consultations', 'assigned_office_id')) {
            Schema::table('consultations', function (Blueprint $table) {
                $table->foreignId('assigned_office_id')
                    ->nullable()
                    ->after('engineer_id')
                    ->constrained('offices')
                    ->nullOnDelete();

                $table->index(
                    ['assigned_office_id', 'status'],
                    'consultations_office_status_idx'
                );
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('consultations', 'assigned_office_id')) {
            Schema::table('consultations', function (Blueprint $table) {
                $table->dropIndex('consultations_office_status_idx');
                $table->dropConstrainedForeignId('assigned_office_id');
            });
        }
    }
};
