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
       Schema::create('student_enrollment', function (Blueprint $table) {
            $table->id();
 
            // Parent/user link
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
 
            // Student's personal information
            $table->string('first_name', 100);
            $table->string('middle_name', 100)->default('N/A');
            $table->string('last_name', 100);
            $table->string('suffix', 20)->nullable();
            $table->string('lrn', 20)->default('N/A');
            $table->string('grade_level', 20);
            $table->date('birthday');
            $table->string('birth_place', 150);
            $table->text('address');
 
            // Family information
            $table->string('mother_name', 150);
            $table->string('father_name', 150);
            $table->string('guardian_name', 150);
            $table->string('emergency_contact', 20);
 
            // Class preference
            $table->enum('preferred_session', ['AM', 'PM']);
 
            // Payment
            $table->enum('payment_method', ['gcash', 'maya', 'bank_transfer', 'cash']);
            $table->string('proof_of_payment')->nullable();
 
            // Admin tracking
            $table->enum('status', ['pending', 'approved','enrolled'])->default('pending');
 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::dropIfExists('student_enrollment');
    }
};
