<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('support_messages', function (Blueprint $table) {
            $table->enum('sender_type', [
                'customer',
                'bot',
                'employee',
                'admin',
                'system',
            ])
                ->default('customer')
                ->after('sender_id');

            $table->boolean('is_internal')
                ->default(false)
                ->after('message_type');
        });
    }

    public function down(): void
    {
        Schema::table('support_messages', function (Blueprint $table) {
            $table->dropColumn([
                'sender_type',
                'is_internal',
            ]);
        });
    }
};
