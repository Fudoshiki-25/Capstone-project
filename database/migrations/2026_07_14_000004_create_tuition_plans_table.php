<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tuition_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_enrollment_id')->unique()
                ->constrained('student_enrollment')->cascadeOnDelete();
            $table->string('plan_type', 20); // 'monthly' | 'quarterly'
            $table->decimal('total_amount', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tuition_plans');
    }
};
