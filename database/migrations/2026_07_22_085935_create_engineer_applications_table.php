<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('engineer_applications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('specialty_id')
                ->constrained('engineering_specialties')
                ->cascadeOnDelete();

            $table->string('certificate_file');

            $table->string('cv_file')->nullable();

            $table->string('payment_receipt');

            $table->decimal('amount', 10, 2);

            $table->enum('payment_status', [
                'pending',
                'paid',
                'rejected',
            ])->default('pending');

            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
            ])->default('pending');

            $table->text('admin_note')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('engineer_applications');
    }
};
