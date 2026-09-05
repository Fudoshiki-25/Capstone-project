<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Widens tuition_payments.status to a plain string. If it was created
     * as a DB-level enum('unpaid','pending','paid','needs_resubmit'),
     * writing the new 'partial' status (introduced for split/partial
     * payments) would fail at the database level otherwise.
     */
    public function up(): void
    {
        Schema::table('tuition_payments', function (Blueprint $table) {
            $table->string('status', 20)->default('unpaid')->change();
        });
    }

    public function down(): void
    {
        // Intentionally left as string on rollback — reverting to a strict
        // enum here isn't safe without knowing the original constraint list.
    }
};