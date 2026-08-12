<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tracks the last time a due-date reminder was sent for an installment, so
 * the scheduled reminder command (app/Console/Commands/SendTuitionReminders)
 * sends at most one reminder per calendar day per payment instead of
 * re-notifying every time it runs.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tuition_payments', function (Blueprint $table) {
            if (! Schema::hasColumn('tuition_payments', 'reminder_sent_at')) {
                $table->timestamp('reminder_sent_at')->nullable()->after('feedback');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tuition_payments', function (Blueprint $table) {
            $table->dropColumn('reminder_sent_at');
        });
    }
};
