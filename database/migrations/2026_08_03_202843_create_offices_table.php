<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offices', function (Blueprint $table) {
            $table->id();

            $table->foreignId('owner_user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('slug')->unique();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('commercial_registration')->nullable();
            $table->string('license_number')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->text('address')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('cover_path')->nullable();
            $table->text('description')->nullable();

            $table->enum('status', [
                'pending',
                'active',
                'suspended',
                'closed',
                'rejected',
            ])->default('pending');

            $table->enum('subscription_status', [
                'pending',
                'active',
                'expired',
                'cancelled',
            ])->default('pending');

            $table->decimal(
                'monthly_subscription_amount',
                10,
                2
            )->default(1000);

            $table->string('subscription_currency', 3)
                ->default('SAR');

            $table->timestamp('subscription_starts_at')
                ->nullable();

            $table->timestamp('subscription_ends_at')
                ->nullable();

            $table->timestamp('approved_at')->nullable();

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('rejection_reason')->nullable();

            $table->timestamp('suspended_at')->nullable();

            $table->foreignId('suspended_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('suspension_reason')->nullable();

            $table->timestamp('closed_at')->nullable();

            $table->foreignId('closed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('closure_reason')->nullable();

            $table->timestamps();

            $table->index([
                'status',
                'subscription_status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offices');
    }
};
