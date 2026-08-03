<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'consultation_office_assignments',
            function (Blueprint $table) {
                $table->id();

                $table->foreignId('consultation_id')
                    ->constrained('consultations')
                    ->cascadeOnDelete();

                $table->foreignId('office_id')
                    ->constrained('offices')
                    ->cascadeOnDelete();

                $table->foreignId('assigned_by')
                    ->constrained('users')
                    ->restrictOnDelete();

                $table->timestamp('assigned_at');

                $table->timestamp('unassigned_at')
                    ->nullable();

                $table->enum('status', [
                    'assigned',
                    'returned',
                    'completed',
                    'cancelled',
                ])->default('assigned');

                $table->text('notes')->nullable();

                $table->timestamps();

                $table->index([
                    'consultation_id',
                    'status',
                ]);

                $table->index([
                    'office_id',
                    'status',
                ]);
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'consultation_office_assignments'
        );
    }
};
