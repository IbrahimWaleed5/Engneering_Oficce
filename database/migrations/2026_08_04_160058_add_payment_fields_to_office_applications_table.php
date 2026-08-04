<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('office_applications', function (Blueprint $table) {
            $table
                ->string('payment_method', 50)
                ->default('bank_transfer')
                ->after('license_document_path');

            $table
                ->string('payment_reference', 190)
                ->nullable()
                ->after('payment_method');

            $table
                ->string('payment_receipt_path')
                ->nullable()
                ->after('payment_reference');

            $table
                ->timestamp('paid_at')
                ->nullable()
                ->after('payment_receipt_path');
        });
    }

    public function down(): void
    {
        Schema::table('office_applications', function (Blueprint $table) {
            $table->dropColumn([
                'payment_method',
                'payment_reference',
                'payment_receipt_path',
                'paid_at',
            ]);
        });
    }
};
