<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollment_requirements', function (Blueprint $table) {
            if (! Schema::hasColumn('enrollment_requirements', 'status')) {
                $table->string('status', 20)->default('pending')->after('path');
            }
            if (! Schema::hasColumn('enrollment_requirements', 'feedback')) {
                $table->text('feedback')->nullable()->after('status');
            }
            if (! Schema::hasColumn('enrollment_requirements', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('feedback');
            }
        });
    }

    public function down(): void
    {
        Schema::table('enrollment_requirements', function (Blueprint $table) {
            $table->dropColumn(['status', 'feedback', 'reviewed_at']);
        });
    }
};
