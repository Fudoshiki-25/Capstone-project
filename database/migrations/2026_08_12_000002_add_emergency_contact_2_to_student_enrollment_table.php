<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_enrollment', function (Blueprint $table) {
            if (! Schema::hasColumn('student_enrollment', 'emergency_contact_2')) {
                $table->string('emergency_contact_2', 20)->nullable()->after('emergency_contact');
            }
        });
    }

    public function down(): void
    {
        Schema::table('student_enrollment', function (Blueprint $table) {
            $table->dropColumn('emergency_contact_2');
        });
    }
};
