<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            $table->string('invoice_number')
                ->unique();

            $table->foreignId('payment_id')
                ->unique()
                ->constrained('payments')
                ->cascadeOnDelete();

            $table->foreignId('consultation_id')
                ->constrained('consultations')
                ->cascadeOnDelete();

            $table->foreignId('customer_id')
                ->constrained('users')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | بيانات ثابتة داخل الفاتورة
            |--------------------------------------------------------------------------
            */

            $table->string('consultation_number');

            $table->string('customer_name');

            $table->string('service_name');

            $table->string('engineer_name')
                ->nullable();

            $table->decimal('amount', 10, 2);

            $table->string('payment_method');

            $table->string('currency')
                ->default('ILS');

            $table->string('office_name')
                ->default('مكتب الوليد الهندسي');

            $table->string('office_logo')
                ->nullable();

            $table->timestamp('issued_at');

            $table->timestamps();

            $table->index('invoice_number');
            $table->index('issued_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
