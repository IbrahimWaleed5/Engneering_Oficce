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
                $table->foreignId(
                    'assigned_office_id'
                )
                    ->nullable()
                    ->after('engineer_id')
                    ->constrained('offices')
                    ->nullOnDelete();

                $table->foreignId(
                    'office_assigned_by'
                )
                    ->nullable()
                    ->after('assigned_office_id')
                    ->constrained('users')
                    ->nullOnDelete();

                $table->timestamp(
                    'office_assigned_at'
                )
                    ->nullable()
                    ->after('office_assigned_by');
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'consultations',
            function (Blueprint $table) {
                $table->dropForeign([
                    'assigned_office_id',
                ]);

                $table->dropForeign([
                    'office_assigned_by',
                ]);

                $table->dropColumn([
                    'assigned_office_id',
                    'office_assigned_by',
                    'office_assigned_at',
                ]);
            }
        );
    }
};
