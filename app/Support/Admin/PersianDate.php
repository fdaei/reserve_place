<?php

namespace App\Support\Admin;

use Carbon\Carbon;
use Morilog\Jalali\Jalalian;

class PersianDate
{
    public static function formatForDisplay(mixed $value, bool $withTime = false): ?string
    {
        if (blank($value)) {
            return null;
        }

        try {
            $normalized = trim(convertPersianToEnglishNumbers((string) $value));
            $normalized = str_replace('T', ' ', $normalized);

            if (preg_match('/^(\d{4})[\/-](\d{1,2})[\/-](\d{1,2})(?:\s+(\d{1,2}):(\d{1,2}))?/', $normalized, $matches)) {
                $year = (int) $matches[1];

                if ($year < 1700) {
                    $date = sprintf('%04d/%02d/%02d', $year, (int) $matches[2], (int) $matches[3]);
                    $time = isset($matches[4]) ? sprintf(' %02d:%02d', (int) $matches[4], (int) ($matches[5] ?? 0)) : '';

                    return $withTime ? $date.$time : $date;
                }
            }

            $jalali = Jalalian::fromDateTime($value);

            return $withTime
                ? $jalali->format('Y/m/d H:i')
                : $jalali->format('Y/m/d');
        } catch (\Throwable) {
            return null;
        }
    }

    public static function parse(?string $value, string $type = 'date'): ?string
    {
        if (blank($value)) {
            return null;
        }

        $value = trim(convertPersianToEnglishNumbers((string) $value));
        $normalized = str_replace('T', ' ', $value);

        try {
            if (preg_match('/^\d{4}-\d{2}-\d{2}( \d{2}:\d{2}(:\d{2})?)?$/', $normalized)) {
                $carbon = Carbon::parse($normalized);

                return $type === 'datetime-local'
                    ? $carbon->format('Y-m-d H:i:s')
                    : $carbon->toDateString();
            }

            [$datePart, $timePart] = array_pad(explode(' ', str_replace('-', '/', $normalized), 2), 2, null);
            [$year, $month, $day] = array_map('intval', explode('/', $datePart));

            if ($year > 1700) {
                $carbon = Carbon::createFromFormat('Y/m/d'.($timePart ? ' H:i' : ''), $datePart.($timePart ? ' '.$timePart : ''));

                return $type === 'datetime-local'
                    ? $carbon->format('Y-m-d H:i:s')
                    : $carbon->toDateString();
            }

            $jalali = Jalalian::fromFormat('Y/m/d', sprintf('%04d/%02d/%02d', $year, $month, $day));
            $carbon = $jalali->toCarbon()->startOfDay();

            if ($type === 'datetime-local' && $timePart) {
                [$hour, $minute] = array_map('intval', explode(':', $timePart));
                $carbon->setTime($hour, $minute);

                return $carbon->format('Y-m-d H:i:s');
            }

            return $type === 'datetime-local'
                ? $carbon->format('Y-m-d H:i:s')
                : $carbon->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }
}
