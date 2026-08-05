<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'support_tickets',
            function (Blueprint $table) {
                $table
                    ->boolean('is_escalated')
                    ->default(false)
                    ->after('assigned_employee_id');

                $table
                    ->foreignId('escalated_by')
                    ->nullable()
                    ->after('is_escalated')
                    ->constrained('users')
                    ->nullOnDelete();

                $table
                    ->timestamp('escalated_at')
                    ->nullable()
                    ->after('escalated_by');

                $table
                    ->text('escalation_reason')
                    ->nullable()
                    ->after('escalated_at');
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'support_tickets',
            function (Blueprint $table) {
                $table->dropForeign([
                    'escalated_by',
                ]);

                $table->dropColumn([
                    'is_escalated',
                    'escalated_by',
                    'escalated_at',
                    'escalation_reason',
                ]);
            }
        );
    }
};
