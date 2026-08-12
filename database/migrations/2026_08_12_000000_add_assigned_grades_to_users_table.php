<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Replaces the single-grade `assigned_grade` column with a JSON
 * `assigned_grades` array, so a super admin can scope one admin account
 * to multiple grade levels (previously only ever one, or unrestricted).
 * Null/empty stays "All Grades", same convention as before.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'assigned_grades')) {
                $table->json('assigned_grades')->nullable()->after('role');
            }
        });

        if (Schema::hasColumn('users', 'assigned_grade')) {
            DB::table('users')->whereNotNull('assigned_grade')->get(['id', 'assigned_grade'])->each(function ($user) {
                DB::table('users')->where('id', $user->id)->update([
                    'assigned_grades' => json_encode([$user->assigned_grade]),
                ]);
            });

            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('assigned_grade');
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'assigned_grade')) {
                $table->string('assigned_grade')->nullable()->after('role');
            }
        });

        if (Schema::hasColumn('users', 'assigned_grades')) {
            DB::table('users')->whereNotNull('assigned_grades')->get(['id', 'assigned_grades'])->each(function ($user) {
                $grades = json_decode($user->assigned_grades, true) ?: [];
                DB::table('users')->where('id', $user->id)->update([
                    'assigned_grade' => $grades[0] ?? null,
                ]);
            });

            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('assigned_grades');
            });
        }
    }
};
