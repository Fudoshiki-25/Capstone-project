<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds 'draft' as a new status: a row in this state means Step 1 has
     * been saved but the parent hasn't finished Step 2 + clicked "Enroll Now"
     * yet. Rows in 'draft' are NOT shown as cards on the Home tab — only
     * 'pending' and later statuses are. This uses raw SQL because Laravel's
     * Blueprint::enum() doesn't support modifying an existing enum column
     * across all database drivers.
     */
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE student_enrollment MODIFY COLUMN status ENUM('draft', 'pending', 'approved', 'enrolled') NOT NULL DEFAULT 'draft'");
        } else {
            // SQLite / other drivers: enums are typically just strings with
            // app-level validation, so no schema change is needed there.
        }
    }

    /**
     * Reverse the migrations.
     *
     * NOTE: rolling this back will fail if any row currently has
     * status = 'draft', since 'draft' won't be a valid value anymore.
     * Convert any draft rows to 'pending' (or delete them) before rolling back.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE student_enrollment MODIFY COLUMN status ENUM('pending', 'approved', 'enrolled') NOT NULL DEFAULT 'pending'");
        }
    }
};