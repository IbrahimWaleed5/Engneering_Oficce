<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('support_tickets', function (Blueprint $table) {
            $table->enum('support_mode', [
                'bot',
                'waiting_employee',
                'employee',
            ])
                ->default('bot')
                ->after('status');

            $table->enum('category', [
                'technical',
                'payment',
                'consultation',
                'account',
                'engineering_office',
                'privacy',
                'appeal',
                'complaint',
                'other',
            ])
                ->default('technical')
                ->after('subject');

            $table->decimal(
                'bot_confidence',
                5,
                4
            )->nullable();

            $table->boolean('bot_resolved')
                ->default(false);

            $table->timestamp('transferred_to_employee_at')
                ->nullable();

            $table->timestamp('first_response_at')
                ->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('support_tickets', function (Blueprint $table) {
            $table->dropColumn([
                'support_mode',
                'category',
                'bot_confidence',
                'bot_resolved',
                'transferred_to_employee_at',
                'first_response_at',
            ]);
        });
    }
};
