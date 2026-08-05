<?php

namespace App\Notifications;

use App\Models\EnrollmentRequirement;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class DocumentNeedsResubmit extends Notification
{
    use Queueable;

    public function __construct(private EnrollmentRequirement $requirement)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', WebPushChannel::class];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $enrollment = $this->requirement->enrollment;
        $name = trim($enrollment->first_name . ' ' . $enrollment->last_name);

        $message = (new MailMessage)
            ->subject('Document Needs Resubmission — ' . $name)
            ->greeting('Hello ' . $notifiable->first_name . ',')
            ->line('The **' . $this->requirement->document_label . '** you uploaded for **' . $name . '** needs to be resubmitted.');

        if ($this->requirement->feedback) {
            $message->line('Admin note: "' . $this->requirement->feedback . '"');
        }

        return $message
            ->line('Please log in to the parent portal to upload a corrected file.')
            ->salutation('— Premiere Heights Learning Center, Inc.');
    }

    public function toWebPush(object $notifiable, $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title('Document Needs Resubmission')
            ->body($this->requirement->document_label . ' needs to be resubmitted.')
            ->tag('requirement-' . $this->requirement->id)
            ->requireInteraction(true)
            ->data(['url' => '/parent']);
    }
}
