<?php

namespace App\Notifications;

use App\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class NewAnnouncementPosted extends Notification
{
    use Queueable;

    public function __construct(private Announcement $announcement)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', WebPushChannel::class];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Announcement — ' . $this->announcement->title)
            ->greeting('Hello ' . $notifiable->first_name . ',')
            ->line('**' . $this->announcement->title . '**')
            ->line($this->announcement->message)
            ->salutation('— Premiere Heights Learning Center, Inc.');
    }

    public function toWebPush(object $notifiable, $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title($this->announcement->title)
            ->body(\Illuminate\Support\Str::limit($this->announcement->message, 120))
            ->tag('announcement-' . $this->announcement->id)
            ->data(['url' => '/parent']);
    }
}
