<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds the fields needed for the Super Admin "Admin Accounts" tab
     * to the existing users table, rather than creating a separate table.
     *
     * - assigned_grade: optional, not enforced (only 1 admin handles all
     *   grades right now, but the column stays for future multi-admin use)
     * - is_active: active/inactive toggle for the admin account
     * - last_login_at: populated on login to show "Last Login" in the table
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'assigned_grade')) {
                $table->string('assigned_grade')->nullable()->after('role');
            }
            if (! Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('assigned_grade');
            }
            if (! Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('is_active');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['assigned_grade', 'is_active', 'last_login_at']);
        });
    }
};