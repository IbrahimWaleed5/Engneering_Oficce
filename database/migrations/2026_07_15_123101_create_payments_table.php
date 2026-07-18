<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('consultation_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('customer_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->decimal('amount', 10, 2);

            $table->enum('payment_method', [
                'cash',
                'card',
                'bank',
                'wallet',
            ]);

            $table->string('transaction_reference')
                ->nullable();

            $table->string('receipt_image')
                ->nullable();

            $table->enum('status', [
                'pending',
                'completed',
                'rejected',
            ])->default('pending');

            $table->timestamp('paid_at')
                ->nullable();

            $table->text('rejection_reason')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
