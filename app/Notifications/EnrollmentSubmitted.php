<?php

namespace App\Notifications;

use App\Models\StudentEnrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class EnrollmentSubmitted extends Notification
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
            ->subject('Enrollment Submitted — ' . $name)
            ->greeting('Hello ' . $notifiable->first_name . ',')
            ->line('Your enrollment application for **' . $name . '** (' . $this->enrollment->grade_level . ') has been submitted successfully.')
            ->line('It is now awaiting review by the school admin. We\'ll let you know as soon as there\'s an update.')
            ->salutation('— Premiere Heights Learning Center, Inc.');
    }

    public function toWebPush(object $notifiable, $notification): WebPushMessage
    {
        $name = trim($this->enrollment->first_name . ' ' . $this->enrollment->last_name);

        return (new WebPushMessage)
            ->title('Enrollment Submitted')
            ->body($name . '\'s application is now awaiting admin review.')
            ->tag('enrollment-' . $this->enrollment->id)
            ->data(['url' => '/parent']);
    }
}
