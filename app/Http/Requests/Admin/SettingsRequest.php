<?php

namespace App\Http\Requests\Admin;

use App\Models\Config;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $settings = $this->input('settings', []);

        foreach (['contact-phone', 'support-phone', 'support-phone-secondary', 'sms_test_mobile'] as $phoneKey) {
            if (isset($settings[$phoneKey])) {
                $settings[$phoneKey] = preg_replace('/\D+/', '', convertPersianToEnglishNumbers((string) $settings[$phoneKey]));
            }
        }

        foreach ([
            'payment_commission_percent',
            'payment_min_withdraw_amount',
            'payment_daily_withdraw_limit',
            'payment_release_hours',
        ] as $numberKey) {
            if (isset($settings[$numberKey])) {
                $settings[$numberKey] = preg_replace('/[^\d.]+/', '', convertPersianToEnglishNumbers((string) $settings[$numberKey]));
            }
        }

        foreach (['instagram', 'telegram', 'whatsapp', 'linkedin'] as $urlKey) {
            if (isset($settings[$urlKey])) {
                $settings[$urlKey] = trim((string) $settings[$urlKey]);
            }
        }

        $this->merge(['settings' => $settings]);
    }

    public function rules(): array
    {
        $baseRules = [
            'settings_scope' => ['required', Rule::in(['general', 'payment', 'sms', 'seo', 'seasonal'])],
            'settings' => ['array'],
            'settings.*' => ['nullable'],
        ];

        return $baseRules + match ($this->input('settings_scope', 'general')) {
            'payment' => $this->paymentRules(),
            'sms' => $this->smsRules(),
            'seo' => $this->seoRules(),
            'seasonal' => $this->seasonalRules(),
            default => $this->generalRules(),
        };
    }

    protected function generalRules(): array
    {
        return [
            'settings.website-title' => ['required', 'string', 'max:255'],
            'settings.website-titleEn' => ['nullable', 'string', 'max:255'],
            'settings.website-description' => ['nullable', 'string', 'max:1000'],
            'settings.website-words' => ['nullable', 'string', 'max:1000'],
            'settings.website-icon' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'settings.default-site-image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'settings.mainColor' => ['nullable', 'string', 'max:20'],
            'settings.secondaryColor' => ['nullable', 'string', 'max:20'],
            'settings.bannerSeasonImage' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'settings.mainBannerText' => ['nullable', 'string', 'max:1000'],
            'settings.websiteStatus' => ['nullable', 'string', 'max:255'],
            'settings.OfflineModeText' => ['nullable', 'string', 'max:1000'],
            'settings.offlineModeIcon' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'settings.contact-phone' => ['nullable', 'digits_between:10,11'],
            'settings.support-phone' => ['nullable', 'digits_between:10,11'],
            'settings.support-phone-secondary' => ['nullable', 'digits_between:10,11'],
            'settings.support-email' => ['nullable', 'email', 'max:255'],
            'settings.contact-address' => ['nullable', 'string', 'max:1000'],
            'settings.office-address' => ['nullable', 'string', 'max:1000'],
            'settings.instagram' => ['nullable', 'url:http,https'],
            'settings.telegram' => ['nullable', 'url:http,https'],
            'settings.whatsapp' => ['nullable', 'url:http,https'],
            'settings.linkedin' => ['nullable', 'url:http,https'],
            'settings.site_revenue_mode' => ['nullable', 'string', 'in:free,professional'],
        ];
    }

    protected function paymentRules(): array
    {
        return [
            'settings.payment_gateway' => ['nullable', 'string', 'max:80'],
            'settings.payment_merchant_code' => ['nullable', 'string', 'max:255'],
            'settings.payment_api_key' => ['nullable', 'string', 'max:255'],
            'settings.payment_commission_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'settings.payment_min_withdraw_amount' => ['nullable', 'integer', 'min:0'],
            'settings.payment_daily_withdraw_limit' => ['nullable', 'integer', 'min:0'],
        ];
    }

    protected function smsRules(): array
    {
        return [
            'settings.sms_service' => ['nullable', 'string', 'max:80'],
            'settings.sms_sender_line' => ['nullable', 'string', 'max:80'],
            'settings.sms_username' => ['nullable', 'string', 'max:255'],
            'settings.sms_password' => ['nullable', 'string', 'max:255'],
            'settings.sms_welcome_text' => ['nullable', 'string', 'max:1000'],
        ];
    }

    protected function seoRules(): array
    {
        $rules = [
            'settings.website-title' => ['required', 'string', 'max:255'],
            'settings.website-description' => ['required', 'string', 'max:1000'],
            'settings.website-words' => ['required', 'string', 'max:1000'],
            'settings.seo_favicon' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,ico', 'max:2048'],
            'settings.website-icon' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];

        if ($this->storedSettingIsBlank('seo_favicon')) {
            $rules['settings.seo_favicon'] = ['required', 'image', 'mimes:jpg,jpeg,png,webp,ico', 'max:2048'];
        }

        if ($this->storedSettingIsBlank('website-icon')) {
            $rules['settings.website-icon'] = ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'];
        }

        return $rules;
    }

    protected function seasonalRules(): array
    {
        return [
            'settings.season_spring_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'settings.season_summer_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'settings.season_autumn_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'settings.season_winter_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }

    protected function storedSettingIsBlank(string $key): bool
    {
        return ! Config::query()
            ->where('title', $key)
            ->whereNotNull('value')
            ->where('value', '!=', '')
            ->exists();
    }

    public function messages(): array
    {
        return [
            'required' => 'وارد کردن :attribute الزامی است.',
            'integer' => ':attribute باید عدد صحیح باشد.',
            'numeric' => ':attribute باید عدد باشد.',
            'image' => ':attribute باید تصویر باشد.',
            'mimes' => ':attribute باید یکی از انواع مجاز باشد: :values.',
            'url' => ':attribute باید آدرس معتبر باشد.',
        ];
    }

    public function attributes(): array
    {
        return [
            'settings.website-title' => 'عنوان سایت',
            'settings.website-description' => 'توضیحات متا',
            'settings.website-words' => 'کلمات کلیدی',
            'settings.website-icon' => 'آیکن سایت',
            'settings.seo_favicon' => 'فاوآیکن',
            'settings.bannerSeasonImage' => 'تصویر بنر فصلی',
            'settings.offlineModeIcon' => 'تصویر حالت تعمیرات',
            'settings.instagram' => 'لینک اینستاگرام',
            'settings.telegram' => 'لینک تلگرام',
            'settings.payment_gateway' => 'درگاه پرداخت',
            'settings.payment_merchant_code' => 'مرچنت کد',
            'settings.payment_api_key' => 'API Key',
            'settings.payment_commission_percent' => 'درصد کمیسیون',
            'settings.payment_min_withdraw_amount' => 'حداقل برداشت',
            'settings.payment_daily_withdraw_limit' => 'حداکثر برداشت روزانه',
            'settings.sms_service' => 'ارائه‌دهنده پیامک',
            'settings.sms_sender_line' => 'شماره خط ارسال‌کننده',
            'settings.sms_username' => 'نام کاربری پیامک',
            'settings.sms_password' => 'رمز عبور پیامک',
            'settings.sms_welcome_text' => 'پیامک خوش‌آمدگویی',
        ];
    }
}
