<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'office_applications',
            function (Blueprint $table) {
                $table->id();

                $table->foreignId('user_id')
                    ->constrained('users')
                    ->cascadeOnDelete();

                $table->string('office_name');
                $table->string('email');
                $table->string('phone')->nullable();
                $table->string(
                    'commercial_registration'
                )->nullable();
                $table->string('license_number')->nullable();
                $table->string('country')->nullable();
                $table->string('city')->nullable();
                $table->text('address')->nullable();
                $table->text('notes')->nullable();

                $table->string(
                    'commercial_registration_path'
                )->nullable();

                $table->string('license_document_path')
                    ->nullable();

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

                $table->timestamp('reviewed_at')->nullable();
                $table->text('rejection_reason')->nullable();

                $table->timestamps();

                $table->index([
                    'user_id',
                    'status',
                ]);
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('office_applications');
    }
};
