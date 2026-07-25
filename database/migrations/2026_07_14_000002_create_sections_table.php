<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->string('grade_level');
            $table->string('name'); // e.g. "Grade 7 - Section 1"
            $table->string('school_year')->nullable();
            $table->timestamps();
        });

        Schema::table('student_enrollment', function (Blueprint $table) {
            $table->foreignId('section_id')->nullable()->after('grade_level')
                ->constrained('sections')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('student_enrollment', function (Blueprint $table) {
            $table->dropConstrainedForeignId('section_id');
        });

        Schema::dropIfExists('sections');
    }
};
