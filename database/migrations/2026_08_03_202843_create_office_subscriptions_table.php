<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'office_subscriptions',
            function (Blueprint $table) {
                $table->id();

                $table->foreignId('office_id')
                    ->constrained('offices')
                    ->cascadeOnDelete();

                $table->decimal('amount', 10, 2)
                    ->default(1000);

                $table->string('currency', 3)
                    ->default('SAR');

                $table->timestamp('starts_at')->nullable();
                $table->timestamp('ends_at')->nullable();

                $table->enum('status', [
                    'pending',
                    'under_review',
                    'active',
                    'expired',
                    'rejected',
                    'cancelled',
                ])->default('pending');

                $table->string('payment_method')->nullable();
                $table->string(
                    'payment_reference'
                )->nullable();
                $table->string('receipt_path')->nullable();
                $table->timestamp('paid_at')->nullable();

                $table->foreignId('approved_by')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table->timestamp('approved_at')->nullable();
                $table->text('rejection_reason')->nullable();
                $table->text('notes')->nullable();

                $table->timestamps();

                $table->index([
                    'office_id',
                    'status',
                ]);
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('office_subscriptions');
    }
};
