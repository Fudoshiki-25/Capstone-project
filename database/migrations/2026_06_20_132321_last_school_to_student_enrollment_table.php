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
        Schema::table('student_enrollment', function (Blueprint $table) {
            // Only collected for new students; nullable since old students don't fill this in.
            $table->string('last_school', 150)->nullable()->after('birth_place');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_enrollment', function (Blueprint $table) {
            $table->dropColumn('last_school');
        });
    }
};