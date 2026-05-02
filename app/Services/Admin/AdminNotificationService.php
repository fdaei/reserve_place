<?php

namespace App\Services\Admin;

use App\Models\AdminNotification;
use App\Models\User;
use App\Notifications\Admin\AdminBroadcastNotification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;

class AdminNotificationService
{
    public function send(AdminNotification $notification): void
    {
        $recipients = $this->resolveRecipients($notification);

        if ($recipients->isEmpty()) {
            $notification->update([
                'status' => 'failed',
                'error_message' => 'گیرنده‌ای برای ارسال اعلان پیدا نشد.',
            ]);

            return;
        }

        try {
            if (Schema::hasTable('notifications')) {
                Notification::send($recipients, new AdminBroadcastNotification($notification));
            }

            $notification->update([
                'status' => 'sent',
                'sent_at' => now(),
                'error_message' => null,
            ]);
        } catch (\Throwable $exception) {
            report($exception);

            $notification->update([
                'status' => 'failed',
                'error_message' => $exception->getMessage(),
            ]);
        }
    }

    protected function resolveRecipients(AdminNotification $notification): Collection
    {
        $query = User::query();

        return match ($notification->audience) {
            'specific_user' => $query->whereKey($notification->user_id)->get(),
            'employees' => $query->whereHas('roles', fn ($role) => $role->where('slug', config('access-control.employee_role')))->get(),
            'hosts' => $query->whereHas('roles', fn ($role) => $role->where('slug', config('access-control.host_role')))->get(),
            default => $query->get(),
        };
    }
}
