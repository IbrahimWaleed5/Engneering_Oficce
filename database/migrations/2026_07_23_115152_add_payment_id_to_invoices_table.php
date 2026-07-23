<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('invoices', 'invoice_number')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->string('invoice_number')
                    ->nullable()
                    ->unique();
            });
        }

        if (! Schema::hasColumn('invoices', 'payment_id')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->foreignId('payment_id')
                    ->nullable()
                    ->constrained('payments')
                    ->cascadeOnDelete();
            });
        }

        if (! Schema::hasColumn('invoices', 'consultation_id')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->foreignId('consultation_id')
                    ->nullable()
                    ->constrained('consultations')
                    ->cascadeOnDelete();
            });
        }

        if (! Schema::hasColumn('invoices', 'customer_id')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->foreignId('customer_id')
                    ->nullable()
                    ->constrained('users')
                    ->cascadeOnDelete();
            });
        }

        if (! Schema::hasColumn('invoices', 'consultation_number')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->string('consultation_number')
                    ->nullable();
            });
        }

        if (! Schema::hasColumn('invoices', 'customer_name')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->string('customer_name')
                    ->nullable();
            });
        }

        if (! Schema::hasColumn('invoices', 'service_name')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->string('service_name')
                    ->nullable();
            });
        }

        if (! Schema::hasColumn('invoices', 'engineer_name')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->string('engineer_name')
                    ->nullable();
            });
        }

        if (! Schema::hasColumn('invoices', 'amount')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->decimal('amount', 10, 2)
                    ->nullable();
            });
        }

        if (! Schema::hasColumn('invoices', 'payment_method')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->string('payment_method')
                    ->nullable();
            });
        }

        if (! Schema::hasColumn('invoices', 'currency')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->string('currency')
                    ->default('ILS');
            });
        }

        if (! Schema::hasColumn('invoices', 'office_name')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->string('office_name')
                    ->default('مكتب الوليد الهندسي');
            });
        }

        if (! Schema::hasColumn('invoices', 'office_logo')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->string('office_logo')
                    ->nullable();
            });
        }

        if (! Schema::hasColumn('invoices', 'issued_at')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->timestamp('issued_at')
                    ->nullable();
            });
        }
    }

    public function down(): void
    {
        // لا نحذف الأعمدة حتى لا نفقد بيانات الفواتير الموجودة.
    }
};
