<?php

namespace App\Services\Sms;

use App\Models\SmsLog;
use Illuminate\Support\Facades\Log;

class SmsService
{
    public function __construct(protected ?SmsProviderInterface $provider = null)
    {
        $this->provider ??= new NullSmsProvider();
    }

    public function send(SmsLog $smsLog): void
    {
        try {
            $result = $this->provider->send($smsLog->phone, $smsLog->message);

            $smsLog->update([
                'provider' => $result['provider'] ?? 'unknown',
                'status' => ! empty($result['success']) ? 'sent' : 'failed',
                'response' => $result['response'] ?? null,
                'sent_at' => ! empty($result['success']) ? now() : null,
                'error_message' => empty($result['success']) ? ($result['response'] ?? 'ارسال پیامک ناموفق بود.') : null,
            ]);
        } catch (\Throwable $exception) {
            Log::error('sms.send.failed', [
                'sms_log_id' => $smsLog->id,
                'phone' => $smsLog->phone,
                'error' => $exception->getMessage(),
            ]);

            $smsLog->update([
                'status' => 'failed',
                'error_message' => $exception->getMessage(),
            ]);
        }
    }
}
