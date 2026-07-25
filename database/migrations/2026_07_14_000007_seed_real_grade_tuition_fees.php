<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Real rates from the official "PHLCI List of Fees SY 2026-2027" sheet.
     * Amount = Tuition Fee + Miscellaneous Fee + Books (the "Total" column) —
     * the ₱15,000 "Upon Enrollment" down payment is handled separately via
     * enrollment_periods.enrollment_fee, not folded into this per-grade total.
     */
    private const RATES = [
        'Kinder'   => 29500,
        'Grade 1'  => 29500,
        'Grade 2'  => 31000,
        'Grade 3'  => 32500,
        'Grade 4'  => 32500,
        'Grade 5'  => 32500,
        'Grade 6'  => 34000,
        'Grade 7'  => 34000,
        'Grade 8'  => 34500,
        'Grade 9'  => 34500,
        'Grade 10' => 35500,
    ];

    public function up(): void
    {
        foreach (self::RATES as $grade => $amount) {
            DB::table('grade_tuition_fees')
                ->updateOrInsert(
                    ['grade_level' => $grade],
                    ['annual_amount' => $amount, 'updated_at' => now(), 'created_at' => now()]
                );
        }
    }

    public function down(): void
    {
        // Not reversible to the exact prior state (was all zero) — no-op.
    }
};
