<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('knowledge_base_articles', function (Blueprint $table) {
            $table->id();

            $table->string('question');

            $table->longText('answer');

            $table->string('category')->nullable();

            $table->text('keywords')->nullable();

            $table->boolean('is_active')->default(true);

            $table->unsignedInteger('views')->default(0);

            $table->unsignedInteger('helpful_count')->default(0);

            $table->unsignedInteger('not_helpful_count')->default(0);

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index([
                'category',
                'is_active',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('knowledge_base_articles');
    }
};
