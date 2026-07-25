<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tuition_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tuition_plan_id')->constrained('tuition_plans')->cascadeOnDelete();
            $table->unsignedTinyInteger('installment_number');
            $table->decimal('amount_due', 10, 2);
            $table->date('due_date');
            $table->string('status', 20)->default('unpaid'); // unpaid | pending | paid
            $table->string('proof_of_payment')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('feedback')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tuition_payments');
    }
};
