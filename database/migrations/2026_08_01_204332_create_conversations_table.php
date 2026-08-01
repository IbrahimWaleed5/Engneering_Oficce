<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | نوع المحادثة
            |--------------------------------------------------------------------------
            |
            | consultation: بين العميل والمهندس بعد تأكيد الدفع.
            | direct: محادثة مباشرة يبدأها المدير.
            |
            */

            $table
                ->string('type', 30)
                ->default('direct')
                ->index();

            /*
            |--------------------------------------------------------------------------
            | الاستشارة المرتبطة
            |--------------------------------------------------------------------------
            */

            $table
                ->foreignId('consultation_id')
                ->nullable()
                ->unique()
                ->constrained('consultations')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | منشئ المحادثة
            |--------------------------------------------------------------------------
            */

            $table
                ->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | آخر نشاط
            |--------------------------------------------------------------------------
            */

            $table
                ->timestamp('last_message_at')
                ->nullable()
                ->index();

            $table->timestamps();

            $table->index([
                'type',
                'created_at',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
