<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Lets a parent send several partial proofs against one installment
     * (e.g. ₱950 now, ₱500 later, ₱500 after that) instead of one proof
     * replacing the whole installment. Each row here is one submission;
     * an installment is "paid" once its verified proofs sum to amount_due.
     */
    public function up(): void
    {
        Schema::create('tuition_payment_proofs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tuition_payment_id')->constrained('tuition_payments')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('payment_method');
            $table->string('proof_of_payment');
            // pending: awaiting admin review. verified: counted toward the
            // installment's paid amount. rejected: doesn't count, parent
            // sees the feedback and can submit a fresh proof.
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->timestamp('submitted_at');
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('feedback')->nullable();
            $table->timestamps();
        });

        // Backfill: any installment that already has a single proof
        // submitted under the old one-proof-per-installment model gets a
        // matching row here, so it keeps showing up for admin review /
        // in the parent's history exactly as before.
        $existing = DB::table('tuition_payments')->whereNotNull('proof_of_payment')->get();

        foreach ($existing as $payment) {
            $status = match ($payment->status) {
                'paid'   => 'verified',
                'pending' => 'pending',
                default  => 'rejected', // 'unpaid' with a proof on file means it was rejected
            };

            DB::table('tuition_payment_proofs')->insert([
                'tuition_payment_id' => $payment->id,
                'amount'             => $payment->amount_due,
                'payment_method'     => $payment->payment_method ?? 'cash',
                'proof_of_payment'   => $payment->proof_of_payment,
                'status'             => $status,
                'submitted_at'       => $payment->submitted_at ?? now(),
                'verified_at'        => $payment->paid_at,
                'verified_by'        => $payment->verified_by,
                'feedback'           => $status === 'rejected' ? $payment->feedback : null,
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tuition_payment_proofs');
    }
};