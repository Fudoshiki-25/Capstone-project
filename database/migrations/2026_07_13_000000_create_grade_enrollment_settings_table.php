<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grade_enrollment_settings', function (Blueprint $table) {
            $table->id();
            $table->string('grade_level')->unique(); // e.g. "Kinder", "Grade 1"
            $table->boolean('is_open')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_enrollment_settings');
    }
};
