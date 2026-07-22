<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('engineer_reviews', function (Blueprint $table) {
            $table->id();

            $table->foreignId('consultation_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('customer_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('engineer_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('rating');

            $table->text('comment')
                ->nullable();

            $table->timestamps();

            $table->index([
                'engineer_id',
                'rating',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('engineer_reviews');
    }
};
