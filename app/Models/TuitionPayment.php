<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TuitionPayment extends Model
{
    protected $fillable = [
        'tuition_plan_id',
        'installment_number',
        'amount_due',
        'due_date',
        'status',
        'proof_of_payment',
        'payment_method',
        'submitted_at',
        'paid_at',
        'verified_by',
        'feedback',
        'reminder_sent_at',
    ];

    protected $casts = [
        'amount_due'       => 'decimal:2',
        'due_date'         => 'date',
        'submitted_at'     => 'datetime',
        'paid_at'          => 'datetime',
        'reminder_sent_at' => 'datetime',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(TuitionPlan::class, 'tuition_plan_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
