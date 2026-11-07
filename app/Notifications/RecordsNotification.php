<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class RecordsNotification extends Notification
{
    use Queueable;

    private $mps;
    private $title;
    private $status;
    private $message;
    /**
     * Create a new notification instance.
     */
    public function __construct($mps, $title, $status, $message)
    {
        $this->mps = $mps;
        $this->title = $title;
        $this->status = $status;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toDatabase($notifiable)
    {
        return [
            'mps' => $this->mps,
            'title' => $this->title,
            'status' => $this->status,
            'message' => $this->message
        ];
    }
}
