<?php

namespace App\Notifications\Admin;

use App\Models\AdminNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminBroadcastNotification extends Notification
{
    use Queueable;

    public function __construct(protected AdminNotification $notificationRecord)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => $this->notificationRecord->title,
            'message' => $this->notificationRecord->message,
            'type' => $this->notificationRecord->type,
            'admin_notification_id' => $this->notificationRecord->id,
        ];
    }
}
