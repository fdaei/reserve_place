<?php

namespace App\Services\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class SecurityEventService
{
    public function log(string $event, ?Request $request = null, array $context = [], ?User $user = null, string $level = 'info'): void
    {
        if (! Schema::hasTable('security_events')) {
            return;
        }

        \App\Models\SecurityEvent::query()->create([
            'user_id' => $user?->id ?? auth()->id(),
            'event' => $event,
            'level' => $level,
            'ip_address' => $request?->ip() ?? request()->ip(),
            'user_agent' => substr((string) ($request?->userAgent() ?? request()->userAgent()), 0, 1000),
            'details' => $context ?: null,
        ]);
    }
}
