<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $exists = DB::table('engineering_specialties')
            ->where('name', 'الهندسة الميكانيكية')
            ->exists();

        if (! $exists) {
            DB::table('engineering_specialties')->insert([
                'name' => 'الهندسة الميكانيكية',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('engineering_specialties')
            ->where('name', 'الهندسة الميكانيكية')
            ->delete();
    }
};
