<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'country_code')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('country_code', 2)
                    ->nullable()
                    ->after('email_verified_at');
            });
        }

        if (! Schema::hasColumn('users', 'dial_code')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('dial_code', 8)
                    ->nullable()
                    ->after('country_code');
            });
        }

        if (! Schema::hasColumn('users', 'phone_verified_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('phone_verified_at')
                    ->nullable()
                    ->after('phone');
            });
        }

        /*
         * لا نضيف حقول two_factor هنا؛
         * تمت إضافتها مسبقًا بواسطة:
         * add_two_factor_columns_to_users_table
         */
    }

    public function down(): void
    {
        $columns = [];

        if (Schema::hasColumn('users', 'country_code')) {
            $columns[] = 'country_code';
        }

        if (Schema::hasColumn('users', 'dial_code')) {
            $columns[] = 'dial_code';
        }

        if (Schema::hasColumn('users', 'phone_verified_at')) {
            $columns[] = 'phone_verified_at';
        }

        if ($columns !== []) {
            Schema::table('users', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};
