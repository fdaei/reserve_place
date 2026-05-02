<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NationalCode implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $code = convertPersianToEnglishNumbers((string) $value);
        $code = preg_replace('/\D+/', '', $code ?? '') ?? '';

        if (strlen($code) !== 10) {
            $fail('کد ملی باید دقیقا ۱۰ رقم باشد.');

            return;
        }

        if (preg_match('/^(\d)\1{9}$/', $code)) {
            $fail('کد ملی وارد شده معتبر نیست.');

            return;
        }

        $checkDigit = (int) substr($code, -1);
        $sum = 0;

        foreach (str_split(substr($code, 0, 9)) as $index => $digit) {
            $sum += ((int) $digit) * (10 - $index);
        }

        $remainder = $sum % 11;
        $isValid = $remainder < 2
            ? $checkDigit === $remainder
            : $checkDigit === (11 - $remainder);

        if (! $isValid) {
            $fail('کد ملی وارد شده معتبر نیست.');
        }
    }
}
