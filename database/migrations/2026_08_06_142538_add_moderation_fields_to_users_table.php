<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedTinyInteger('warnings_count')
                ->default(0)
                ->after('status');

            $table->timestamp('suspended_at')
                ->nullable()
                ->after('warnings_count');

            $table->text('suspension_reason')
                ->nullable()
                ->after('suspended_at');

            $table->string('suspension_source', 50)
                ->nullable()
                ->after('suspension_reason');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'warnings_count',
                'suspended_at',
                'suspension_reason',
                'suspension_source',
            ]);
        });
    }
};
