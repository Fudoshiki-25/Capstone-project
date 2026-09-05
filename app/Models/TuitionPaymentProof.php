<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TuitionPaymentProof extends Model
{
    protected $fillable = [
        'tuition_payment_id',
        'amount',
        'verified_amount',
        'payment_method',
        'proof_of_payment',
        'status',
        'submitted_at',
        'verified_at',
        'verified_by',
        'feedback',
    ];

    protected $casts = [
        'amount'          => 'decimal:2',
        'verified_amount' => 'decimal:2',
        'submitted_at'    => 'datetime',
        'verified_at'     => 'datetime',
    ];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(TuitionPayment::class, 'tuition_payment_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * The amount that actually counts toward the installment's balance.
     * Defaults to what the parent claimed (amount) — only differs when an
     * admin corrects it at verify-time (verified_amount), e.g. the receipt
     * shows ₱950 but the parent typed ₱1,000.
     */
    public function creditedAmount(): float
    {
        return (float) ($this->verified_amount ?? $this->amount);
    }
}