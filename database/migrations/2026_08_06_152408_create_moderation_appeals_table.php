<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('moderation_appeals', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            /*
             * التحذير أو المخالفة المرتبطة بالطعن.
             */
            $table->foreignId('user_warning_id')
                ->nullable()
                ->constrained('user_warnings')
                ->nullOnDelete();

            /*
             * pending
             * under_review
             * approved
             * rejected
             * cancelled
             */
            $table->string('status', 30)
                ->default('pending')
                ->index();

            /*
             * رسالة المستخدم للمدير.
             */
            $table->text('message');

            /*
             * مرفق اختياري لإثبات أن القرار خاطئ.
             */
            $table->string('attachment_path')
                ->nullable();

            $table->string('attachment_original_name')
                ->nullable();

            $table->string('attachment_mime_type', 100)
                ->nullable();

            /*
             * رد الإدارة.
             */
            $table->text('admin_response')
                ->nullable();

            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('reviewed_at')
                ->nullable();

            $table->timestamp('resolved_at')
                ->nullable();

            $table->timestamps();

            $table->index([
                'user_id',
                'status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moderation_appeals');
    }
};
