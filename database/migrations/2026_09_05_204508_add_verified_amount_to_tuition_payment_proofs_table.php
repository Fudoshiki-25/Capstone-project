<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tuition_payment_proofs', function (Blueprint $table) {
            // Nullable: null means "trust what the parent claimed" (amount).
            // Only set when an admin corrects the figure at verify-time
            // (e.g. the receipt actually shows a different amount).
            $table->decimal('verified_amount', 10, 2)->nullable()->after('amount');
        });
    }

    public function down(): void
    {
        Schema::table('tuition_payment_proofs', function (Blueprint $table) {
            $table->dropColumn('verified_amount');
        });
    }
};