<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'content_moderations',
            function (Blueprint $table): void {
                $table->string('file_path')->nullable()->change();
                $table->string('original_name')->nullable()->change();
                $table->string('mime_type')->nullable()->change();
                $table->unsignedBigInteger('file_size')->nullable()->change();
                $table->string('file_hash')->nullable()->change();
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'content_moderations',
            function (Blueprint $table): void {
                $table->string('file_path')->nullable(false)->change();
                $table->string('original_name')->nullable(false)->change();
                $table->string('mime_type')->nullable(false)->change();
                $table->unsignedBigInteger('file_size')->nullable(false)->change();
                $table->string('file_hash')->nullable(false)->change();
            }
        );
    }
};
