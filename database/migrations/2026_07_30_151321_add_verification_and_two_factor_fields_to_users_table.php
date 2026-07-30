<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('country_code', 2)
                ->nullable()
                ->after('email_verified_at');

            $table->string('dial_code', 8)
                ->nullable()
                ->after('country_code');

            $table->timestamp('phone_verified_at')
                ->nullable()
                ->after('phone');

            $table->text('two_factor_secret')
                ->nullable()
                ->after('remember_token');

            $table->text('two_factor_recovery_codes')
                ->nullable()
                ->after('two_factor_secret');

            $table->timestamp('two_factor_confirmed_at')
                ->nullable()
                ->after('two_factor_recovery_codes');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unique('phone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['phone']);

            $table->dropColumn([
                'country_code',
                'dial_code',
                'phone_verified_at',
                'two_factor_secret',
                'two_factor_recovery_codes',
                'two_factor_confirmed_at',
            ]);
        });
    }
};
