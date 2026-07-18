<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('engineer_work_images', function (Blueprint $table) {
            $table->id();

            $table->foreignId('engineer_work_id')
                ->constrained('engineer_works')
                ->cascadeOnDelete();

            $table->string('image_path');

            $table->string('caption')->nullable();

            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('engineer_work_images');
    }
};
