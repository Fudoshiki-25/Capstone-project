<?php

namespace App\Notifications;

use App\Models\StudentEnrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class EnrollmentApproved extends Notification
{
    use Queueable;

    public function __construct(private StudentEnrollment $enrollment)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', WebPushChannel::class];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $name = trim($this->enrollment->first_name . ' ' . $this->enrollment->last_name);

        return (new MailMessage)
            ->subject('Application Approved — ' . $name)
            ->greeting('Hello ' . $notifiable->first_name . ',')
            ->line('Good news! The enrollment application for **' . $name . '** (' . $this->enrollment->grade_level . ') has been **approved**.')
            ->line('You can log in to the parent portal to view the next steps.')
            ->salutation('— Premiere Heights Learning Center, Inc.');
    }

    public function toWebPush(object $notifiable, $notification): WebPushMessage
    {
        $name = trim($this->enrollment->first_name . ' ' . $this->enrollment->last_name);

        return (new WebPushMessage)
            ->title('Application Approved')
            ->body($name . '\'s enrollment has been approved.')
            ->tag('enrollment-' . $this->enrollment->id)
            ->requireInteraction(true)
            ->data(['url' => '/parent']);
    }
}
