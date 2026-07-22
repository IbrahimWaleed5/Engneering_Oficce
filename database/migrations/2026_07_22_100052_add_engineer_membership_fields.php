<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string(
                'engineer_membership_status',
                20
            )
                ->nullable()
                ->after('status');

            $table->timestamp('engineer_active_until')
                ->nullable()
                ->after('engineer_membership_status');
        });

        Schema::table(
            'engineer_applications',
            function (Blueprint $table) {
                $table->string('application_type', 20)
                    ->default('new')
                    ->after('amount');

                $table->unsignedInteger('membership_days')
                    ->nullable()
                    ->after('application_type');

                $table->timestamp('membership_started_at')
                    ->nullable()
                    ->after('membership_days');

                $table->timestamp('membership_expires_at')
                    ->nullable()
                    ->after('membership_started_at');

                $table->timestamp('approved_at')
                    ->nullable()
                    ->after('membership_expires_at');
            }
        );

        /*
         * أي مهندس موجود حاليًا بدون اشتراك معتمد
         * يصبح مهندسًا غير نشط إلى أن يدفع.
         */
        DB::table('users')
            ->where('role', 'engineer')
            ->update([
                'engineer_membership_status' => 'inactive',
            ]);
    }

    public function down(): void
    {
        Schema::table(
            'engineer_applications',
            function (Blueprint $table) {
                $table->dropColumn([
                    'application_type',
                    'membership_days',
                    'membership_started_at',
                    'membership_expires_at',
                    'approved_at',
                ]);
            }
        );

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'engineer_membership_status',
                'engineer_active_until',
            ]);
        });
    }
};
