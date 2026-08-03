<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'office_membership_applications',
            function (Blueprint $table) {
                $table->id();

                $table->foreignId('office_id')
                    ->constrained('offices')
                    ->cascadeOnDelete();

                $table->foreignId('engineer_id')
                    ->constrained('users')
                    ->cascadeOnDelete();

                $table->foreignId('specialty_id')
                    ->constrained(
                        'engineering_specialties'
                    )
                    ->restrictOnDelete();

                $table->string(
                    'requested_position'
                )->nullable();

                $table->unsignedTinyInteger(
                    'years_of_experience'
                )->nullable();

                $table->string('cv_path');
                $table->string('certificate_path');
                $table->text('message')->nullable();

                $table->enum('status', [
                    'pending',
                    'approved',
                    'rejected',
                    'cancelled',
                ])->default('pending');

                $table->foreignId('reviewed_by')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table->timestamp('reviewed_at')
                    ->nullable();

                $table->text('rejection_reason')
                    ->nullable();

                $table->timestamps();

                $table->index([
                    'office_id',
                    'engineer_id',
                    'status',
                ]);
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'office_membership_applications'
        );
    }
};
