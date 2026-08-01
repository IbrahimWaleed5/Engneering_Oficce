<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'conversation_messages',
            function (Blueprint $table) {
                $table->id();

                $table
                    ->foreignId('conversation_id')
                    ->constrained('conversations')
                    ->cascadeOnDelete();

                $table
                    ->foreignId('sender_id')
                    ->constrained('users')
                    ->cascadeOnDelete();

                /*
                |--------------------------------------------------------------------------
                | محتوى الرسالة
                |--------------------------------------------------------------------------
                */

                $table
                    ->text('message')
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | نوع الرسالة
                |--------------------------------------------------------------------------
                |
                | text
                | image
                | file
                | voice
                |
                */

                $table
                    ->string('message_type', 30)
                    ->default('text')
                    ->index();

                /*
                |--------------------------------------------------------------------------
                | بيانات المرفق
                |--------------------------------------------------------------------------
                */

                $table
                    ->string('attachment_path')
                    ->nullable();

                $table
                    ->string('attachment_name')
                    ->nullable();

                $table
                    ->string('attachment_mime')
                    ->nullable();

                $table
                    ->unsignedBigInteger('attachment_size')
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | مدة التسجيل الصوتي بالثواني
                |--------------------------------------------------------------------------
                */

                $table
                    ->unsignedInteger('audio_duration')
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | تعديل أو حذف الرسالة
                |--------------------------------------------------------------------------
                */

                $table
                    ->timestamp('edited_at')
                    ->nullable();

                $table
                    ->timestamp('deleted_at')
                    ->nullable();

                $table->timestamps();

                $table->index([
                    'conversation_id',
                    'created_at',
                ]);

                $table->index([
                    'sender_id',
                    'created_at',
                ]);
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'conversation_messages'
        );
    }
};
