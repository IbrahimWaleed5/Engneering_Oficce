<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_moderations', function (Blueprint $table) {
            $table->id();

            /*
             * صاحب المحتوى.
             */
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            /*
             * مكان استخدام الملف، مثل:
             * profile_image
             * portfolio
             * consultation
             * library
             * post
             */
            $table->string('source_type', 100);

            /*
             * رقم السجل المرتبط بالصورة أو الملف.
             * قد يكون null قبل إنشاء السجل الأساسي.
             */
            $table->unsignedBigInteger('source_id')
                ->nullable();

            /*
             * بيانات الملف.
             */
            $table->string('file_path');
            $table->string('original_name')
                ->nullable();

            $table->string('mime_type', 100)
                ->nullable();

            $table->unsignedBigInteger('file_size')
                ->nullable();

            $table->string('file_hash', 64)
                ->nullable()
                ->index();

            /*
             * حالة عملية الفحص:
             * pending
             * processing
             * completed
             * failed
             */
            $table->string('status', 30)
                ->default('pending')
                ->index();

            /*
             * قرار الفحص:
             * approved
             * rejected
             * needs_review
             */
            $table->string('decision', 30)
                ->nullable()
                ->index();

            /*
             * مستوى الخطورة:
             * low
             * medium
             * high
             * critical
             */
            $table->string('risk_level', 30)
                ->nullable()
                ->index();

            /*
             * أنواع المخالفات التي اكتشفها النظام.
             */
            $table->json('detected_categories')
                ->nullable();

            /*
             * النسب التي أعادها نظام الفحص.
             */
            $table->json('category_scores')
                ->nullable();

            /*
             * سبب القرار المختصر.
             */
            $table->text('reason')
                ->nullable();

            /*
             * مقدم خدمة الفحص، مثل Gemini.
             */
            $table->string('provider', 50)
                ->nullable();

            $table->string('model', 100)
                ->nullable();

            /*
             * الرد الخام للاحتفاظ به للمراجعة الفنية.
             * يجب ألا يحتوي على مفاتيح سرية.
             */
            $table->json('provider_response')
                ->nullable();

            /*
             * هل نتج تحذير للمستخدم؟
             */
            $table->boolean('warning_issued')
                ->default(false);

            /*
             * مراجعة الإدارة.
             */
            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('reviewed_at')
                ->nullable();

            $table->text('review_notes')
                ->nullable();

            /*
             * القرار النهائي بعد مراجعة الإدارة.
             */
            $table->string('final_decision', 30)
                ->nullable();

            $table->timestamp('processed_at')
                ->nullable();

            $table->timestamps();

            $table->index([
                'source_type',
                'source_id',
            ]);

            $table->index([
                'user_id',
                'created_at',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'content_moderations'
        );
    }
};
