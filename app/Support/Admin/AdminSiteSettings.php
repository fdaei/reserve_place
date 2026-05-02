<?php

namespace App\Support\Admin;

class AdminSiteSettings
{
    public static function revenueModeOptions(): array
    {
        return [
            'free' => 'رایگان',
            'professional' => 'حرفه‌ای',
        ];
    }

    public static function revenueMode(): string
    {
        $mode = getConfigs('site_revenue_mode', 'free');

        return array_key_exists($mode, self::revenueModeOptions()) ? $mode : 'free';
    }

    public static function revenueModeLabel(?string $mode = null): string
    {
        $mode ??= self::revenueMode();

        return self::revenueModeOptions()[$mode] ?? self::revenueModeOptions()['free'];
    }

    public static function revenueModeIcon(?string $mode = null): string
    {
        return match ($mode ?? self::revenueMode()) {
            'professional' => 'fa-credit-card',
            default => 'fa-phone',
        };
    }
}
