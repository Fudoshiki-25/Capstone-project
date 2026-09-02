<?php

namespace App\Notifications;

use App\Models\TuitionPayment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Fires when a parent submits proof of payment for a tuition installment
 * (see TuitionController::uploadProof()). Admin-facing — read entirely from
 * the database notifications table by NotificationController and rendered
 * in the bell dropdown / notifDetailModal in admin.blade.php.
 *
 * 'database' only for now: admins don't have a push subscription flow yet
 * (only parents do, via PushSubscriptionController) and this doesn't need
 * an email — it's meant to be checked from the dashboard bell.
 */
class TuitionProofSubmitted extends Notification
{
    use Queueable;

    public function __construct(private TuitionPayment $payment)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $enrollment  = $this->payment->plan->enrollment;
        $studentName = trim($enrollment->first_name . ' ' . $enrollment->last_name);
        $parentName  = trim(($enrollment->user->first_name ?? '') . ' ' . ($enrollment->user->last_name ?? ''));

        $label = $this->payment->installment_number === 0
            ? 'Upon Enrollment (Down Payment)'
            : 'Installment ' . $this->payment->installment_number;

        return [
            // Drives the bell icon (bi-cash-coin) and modal routing in the
            // existing admin JS — must stay exactly 'payment_proof_submitted'.
            'type'             => 'payment_proof_submitted',
            'message'          => ($parentName ?: 'A parent') . ' submitted proof of payment for '
                . $studentName . ' — ' . $label,
            'payment_id'       => $this->payment->id,
            'enrollment_id'    => $enrollment->id,
            'student_name'     => $studentName,
            'parent_name'      => $parentName ?: null,
            'label'            => $label,
            'amount_due'       => (float) $this->payment->amount_due,
            'payment_method'   => $this->payment->payment_method,
            'submitted_at'     => $this->payment->submitted_at?->format('M j, Y g:i A'),
            'proof_of_payment' => $this->payment->proof_of_payment
                ? asset('storage/' . $this->payment->proof_of_payment)
                : null,
        ];
    }
}