<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollment_periods', function (Blueprint $table) {
            // Flat "Upon Enrollment" down payment (₱15,000 across all grades per
            // the official fee sheet) — already captured via the Step 1 proof of
            // payment upload, so it's subtracted from the grade total before the
            // remaining balance is split into monthly/quarterly installments.
            if (! Schema::hasColumn('enrollment_periods', 'enrollment_fee')) {
                $table->decimal('enrollment_fee', 10, 2)->default(15000)->after('is_open');
            }
        });

        Schema::table('tuition_plans', function (Blueprint $table) {
            // Snapshot of the down payment at the time this plan was generated,
            // so later changes to enrollment_fee don't retroactively alter it.
            if (! Schema::hasColumn('tuition_plans', 'down_payment')) {
                $table->decimal('down_payment', 10, 2)->default(0)->after('total_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('enrollment_periods', function (Blueprint $table) {
            $table->dropColumn('enrollment_fee');
        });

        Schema::table('tuition_plans', function (Blueprint $table) {
            $table->dropColumn('down_payment');
        });
    }
};
