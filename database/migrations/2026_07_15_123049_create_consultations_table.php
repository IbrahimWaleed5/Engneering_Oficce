<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('consultations', function (Blueprint $table) {
    $table->id();
    $table->string('consultation_number')->unique();
    $table->foreignId('customer_id')->constrained('users');
    $table->foreignId('consultation_type_id')->constrained('consultation_types');
    $table->string('title');
    $table->text('description');
    $table->decimal('final_price', 10, 2);
    $table->enum('status', [
        'pending',
        'in_progress',
        'completed'
    ])->default('pending');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};
