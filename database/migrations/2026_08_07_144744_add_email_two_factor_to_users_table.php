<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('email_two_factor_enabled')
                ->default(false)
                ->after('remember_token');

            $table->timestamp('email_two_factor_verified_at')
                ->nullable()
                ->after('email_two_factor_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'email_two_factor_enabled',
                'email_two_factor_verified_at',
            ]);
        });
    }
};
