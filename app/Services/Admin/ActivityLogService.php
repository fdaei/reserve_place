<?php

namespace App\Services\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ActivityLogService
{
    public function log(string $action, Model|string|null $subject = null, ?Request $request = null, array $properties = [], ?string $description = null, ?int $userId = null): void
    {
        if (! Schema::hasTable('activity_logs')) {
            return;
        }

        \App\Models\ActivityLog::query()->create([
            'user_id' => $userId ?? auth()->id(),
            'action' => $action,
            'model_type' => $subject instanceof Model ? $subject::class : (is_string($subject) ? $subject : null),
            'model_id' => $subject instanceof Model ? $subject->getKey() : null,
            'description' => $description,
            'ip_address' => $request?->ip() ?? request()->ip(),
            'user_agent' => substr((string) ($request?->userAgent() ?? request()->userAgent()), 0, 1000),
            'properties' => $properties ?: null,
        ]);
    }
}
