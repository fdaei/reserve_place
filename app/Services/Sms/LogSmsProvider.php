<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Log;

class LogSmsProvider implements SmsProviderInterface
{
    public function send(string $phone, string $message): array
    {
        Log::info('sms.log_provider.send', [
            'phone' => $phone,
            'message' => $message,
        ]);

        return [
            'success' => true,
            'provider' => 'log',
            'response' => 'SMS logged successfully.',
        ];
    }
}
