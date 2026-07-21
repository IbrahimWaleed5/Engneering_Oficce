<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('engineer_works', function (Blueprint $table) {
            $table->string('pdf_file')
                ->nullable()
                ->after('description');

            $table->string('dwg_file')
                ->nullable()
                ->after('pdf_file');
        });
    }

    public function down(): void
    {
        Schema::table('engineer_works', function (Blueprint $table) {
            $table->dropColumn([
                'pdf_file',
                'dwg_file',
            ]);
        });
    }
};
