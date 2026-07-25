<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_enrollment', function (Blueprint $table) {
            // 'new' = first time at this school, 'old' = returning/already has a record.
            // Determines which documents are required (see EnrollmentController).
            $table->string('student_type', 10)->default('new')->after('grade_level');

            // Chosen by the parent during Step 1; used to generate the
            // installment schedule once the enrollment is finalized.
            $table->string('payment_plan', 20)->nullable()->after('payment_method');
        });
    }

    public function down(): void
    {
        Schema::table('student_enrollment', function (Blueprint $table) {
            $table->dropColumn(['student_type', 'payment_plan']);
        });
    }
};
