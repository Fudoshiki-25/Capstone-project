<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();

            // Who performed the action (nullable so system-generated logs
            // or logs from a deleted user still keep their record).
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('user_name');   // snapshot, survives user deletion
            $table->string('user_role');   // e.g. "Admin", "Super Admin"

            $table->string('action');      // e.g. "Approved Application"
            $table->text('details')->nullable(); // e.g. "Applicant: Jose Madera"

            // Used to color-code the log row in the UI
            // (success, danger, info, purple, warning)
            $table->string('type')->default('info');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};