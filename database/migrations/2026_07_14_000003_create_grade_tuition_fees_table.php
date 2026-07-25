<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grade_tuition_fees', function (Blueprint $table) {
            $table->id();
            $table->string('grade_level')->unique();
            $table->decimal('annual_amount', 10, 2)->default(0);
            $table->timestamps();
        });

        // Seed a starting row per grade level so superadmin can edit rates
        // immediately instead of the table being empty.
        $gradeLevels = ['Kinder','Grade 1','Grade 2','Grade 3','Grade 4','Grade 5','Grade 6','Grade 7','Grade 8','Grade 9','Grade 10'];
        $now = now();
        \Illuminate\Support\Facades\DB::table('grade_tuition_fees')->insert(
            array_map(fn($g) => [
                'grade_level'  => $g,
                'annual_amount' => 0,
                'created_at'   => $now,
                'updated_at'   => $now,
            ], $gradeLevels)
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_tuition_fees');
    }
};
