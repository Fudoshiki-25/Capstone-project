<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tuition_payments', function (Blueprint $table) {
            // How the parent says they paid this specific installment (can
            // differ installment to installment — e.g. GCash one month, cash
            // the next). Distinct from student_enrollment.payment_method,
            // which only covers the initial down payment at enrollment.
            if (! Schema::hasColumn('tuition_payments', 'payment_method')) {
                $table->string('payment_method', 20)->nullable()->after('proof_of_payment');
            }

            // When the parent actually submitted this proof — distinct from
            // updated_at, which also changes again later when admin verifies.
            if (! Schema::hasColumn('tuition_payments', 'submitted_at')) {
                $table->timestamp('submitted_at')->nullable()->after('payment_method');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tuition_payments', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'submitted_at']);
        });
    }
};
