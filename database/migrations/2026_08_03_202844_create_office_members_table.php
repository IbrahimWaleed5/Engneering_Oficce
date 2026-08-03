<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'office_members',
            function (Blueprint $table) {
                $table->id();

                $table->foreignId('office_id')
                    ->constrained('offices')
                    ->cascadeOnDelete();

                $table->foreignId('user_id')
                    ->constrained('users')
                    ->cascadeOnDelete();

                $table->foreignId('specialty_id')
                    ->nullable()
                    ->constrained(
                        'engineering_specialties'
                    )
                    ->nullOnDelete();

                $table->string('position')->nullable();

                $table->enum('office_role', [
                    'owner',
                    'manager',
                    'engineer',
                    'employee',
                ])->default('engineer');

                $table->enum('status', [
                    'active',
                    'suspended',
                    'left',
                ])->default('active');

                $table->foreignId('approved_by')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table->timestamp('joined_at')->nullable();
                $table->timestamp('left_at')->nullable();

                $table->timestamps();

                $table->unique([
                    'office_id',
                    'user_id',
                ]);

                $table->index([
                    'user_id',
                    'status',
                ]);
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('office_members');
    }
};
