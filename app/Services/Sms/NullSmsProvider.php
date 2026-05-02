<?php

namespace App\Services\Sms;

class NullSmsProvider implements SmsProviderInterface
{
    public function send(string $phone, string $message): array
    {
        return [
            'success' => true,
            'provider' => 'null',
            'response' => 'SMS provider is not configured. Message stored as simulated send.',
            'phone' => $phone,
            'message' => $message,
        ];
    }
}
