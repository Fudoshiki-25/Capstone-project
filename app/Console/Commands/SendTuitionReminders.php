<?php

namespace App\Console\Commands;

use App\Models\TuitionPayment;
use App\Notifications\TuitionPaymentDueSoon;
use App\Support\SafeNotify;
use Illuminate\Console\Command;

class SendTuitionReminders extends Command
{
    protected $signature = 'tuition:send-reminders';

    protected $description = 'Emails/pushes a reminder for unpaid tuition installments due within 5 days, or overdue';

    /**
     * Days-before-due-date a reminder starts going out. Anything already
     * past its due date is always included too (the overdue case).
     */
    private const REMIND_DAYS_BEFORE = 5;

    public function handle(): int
    {
        $payments = TuitionPayment::with('plan.enrollment.user')
            ->where('status', 'unpaid')
            ->where('due_date', '<=', now()->addDays(self::REMIND_DAYS_BEFORE))
            ->where(function ($q) {
                // At most one reminder per calendar day per installment —
                // otherwise every run of this command (every minute, via
                // the scheduler) would re-notify the same unpaid payment.
                $q->whereNull('reminder_sent_at')
                    ->orWhereDate('reminder_sent_at', '<', now()->toDateString());
            })
            ->get();

        $sent = 0;

        foreach ($payments as $payment) {
            $enrollment = $payment->plan?->enrollment;
            $parent     = $enrollment?->user;

            if (! $parent) {
                continue;
            }

            SafeNotify::to($parent, new TuitionPaymentDueSoon($payment));
            $payment->update(['reminder_sent_at' => now()]);
            $sent++;
        }

        $this->info("Sent {$sent} tuition reminder(s).");

        return self::SUCCESS;
    }
}
