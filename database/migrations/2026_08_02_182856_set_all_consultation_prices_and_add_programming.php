<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('consultation_types')->update([
            'price' => 40.00,
            'updated_at' => now(),
        ]);

        $programmingType = DB::table('consultation_types')
            ->where('name', 'استشارة برمجية')
            ->first();

        if ($programmingType) {
            DB::table('consultation_types')
                ->where('id', $programmingType->id)
                ->update([
                    'price' => 40.00,
                    'updated_at' => now(),
                ]);
        } else {
            DB::table('consultation_types')->insert([
                'name' => 'استشارة برمجية',
                'price' => 40.00,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('consultation_types')
            ->where('name', 'استشارة برمجية')
            ->delete();
    }
};
