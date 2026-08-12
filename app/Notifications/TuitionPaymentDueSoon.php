<?php

namespace App\Notifications;

use App\Models\TuitionPayment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class TuitionPaymentDueSoon extends Notification
{
    use Queueable;

    public function __construct(private TuitionPayment $payment)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', WebPushChannel::class];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $enrollment = $this->payment->plan->enrollment;
        $name       = trim($enrollment->first_name . ' ' . $enrollment->last_name);
        $isOverdue  = $this->payment->due_date->isPast();
        $amount     = number_format((float) $this->payment->amount_due, 2);
        $dueDate    = $this->payment->due_date->format('F j, Y');

        $subject = $isOverdue
            ? "Overdue Tuition Payment — {$name}"
            : "Tuition Payment Due Soon — {$name}";

        $message = (new MailMessage)
            ->subject($subject)
            ->greeting('Hello ' . $notifiable->first_name . ',');

        if ($isOverdue) {
            $message->line("Installment #{$this->payment->installment_number} for **{$name}** (₱{$amount}) was due on **{$dueDate}** and hasn't been paid yet.");
        } else {
            $message->line("A reminder that installment #{$this->payment->installment_number} for **{$name}** (₱{$amount}) is due on **{$dueDate}**.");
        }

        return $message
            ->line('Please log in to the parent portal to submit your proof of payment.')
            ->salutation('— Premiere Heights Learning Center, Inc.');
    }

    public function toWebPush(object $notifiable, $notification): WebPushMessage
    {
        $enrollment = $this->payment->plan->enrollment;
        $name       = trim($enrollment->first_name . ' ' . $enrollment->last_name);
        $isOverdue  = $this->payment->due_date->isPast();
        $amount     = number_format((float) $this->payment->amount_due, 2);

        return (new WebPushMessage)
            ->title($isOverdue ? 'Overdue Tuition Payment' : 'Tuition Payment Due Soon')
            ->body("{$name} — installment #{$this->payment->installment_number} (₱{$amount}) " . ($isOverdue ? 'is overdue.' : 'is due ' . $this->payment->due_date->diffForHumans() . '.'))
            ->tag('tuition-payment-' . $this->payment->id)
            ->requireInteraction($isOverdue)
            ->data(['url' => '/parent']);
    }
}
