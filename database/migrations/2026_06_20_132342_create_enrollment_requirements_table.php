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
        Schema::create('enrollment_requirements', function (Blueprint $table) {
            $table->id();

            // Link to the enrollment record this document belongs to.
            $table->unsignedBigInteger('enrollment_id');
            $table->foreign('enrollment_id')
                  ->references('id')->on('student_enrollment')
                  ->onDelete('cascade');

            // e.g. 'birth_certificate', 'report_card', 'good_moral', 'id_photo', 'medical_certificate'
            $table->string('document_type', 50);

            // Human-readable label, e.g. "Birth Certificate (PSA)"
            $table->string('document_label', 150);

            // Storage path (relative, e.g. requirements/3/12/birth_certificate.jpg)
            $table->string('path');

            $table->timestamps();

            // A given enrollment can only have ONE current file per document type.
            // Re-uploading the same type should update this row, not create a duplicate.
            $table->unique(['enrollment_id', 'document_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollment_requirements');
    }
};