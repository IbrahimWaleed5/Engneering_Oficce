<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_warnings', function (Blueprint $table) {
            $table->id();

            /*
             * المستخدم الذي حصل على التحذير.
             */
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            /*
             * نتيجة فحص المحتوى التي أدت للتحذير.
             */
            $table->foreignId('content_moderation_id')
                ->nullable()
                ->constrained('content_moderations')
                ->nullOnDelete();

            /*
             * رقم التحذير الفعال للمستخدم:
             * 1 أو 2 أو 3.
             */
            $table->unsignedTinyInteger(
                'warning_number'
            );

            /*
             * سبب التحذير.
             */
            $table->string('category', 100)
                ->nullable();

            $table->text('reason');

            /*
             * مصدر التحذير:
             * ai
             * admin
             * employee
             * system
             */
            $table->string('issued_by_type', 30)
                ->default('system');

            $table->foreignId('issued_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
             * حالة التحذير:
             * active
             * cancelled
             * appealed
             * confirmed
             */
            $table->string('status', 30)
                ->default('active')
                ->index();

            /*
             * هل تسبب التحذير بتعليق الحساب؟
             */
            $table->boolean(
                'account_suspended'
            )->default(false);

            /*
             * بيانات مراجعة الإدارة.
             */
            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('reviewed_at')
                ->nullable();

            $table->text('review_notes')
                ->nullable();

            $table->timestamp('cancelled_at')
                ->nullable();

            $table->timestamps();

            $table->index([
                'user_id',
                'status',
            ]);

            $table->index([
                'user_id',
                'warning_number',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'user_warnings'
        );
    }
};
