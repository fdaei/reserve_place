<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SettingsRequest;
use App\Models\Config;
use App\Models\SmsLog;
use App\Services\Admin\ActivityLogService;
use App\Services\Sms\SmsService;
use App\Support\Admin\AdminFileManager;
use App\Support\Admin\AdminSiteSettings;
use Illuminate\Http\UploadedFile;

class SettingsController extends Controller
{
    public function edit()
    {
        abort_unless(auth()->user()?->hasPermissionBySlug('settings-manage'), 403);

        return view('admin.settings.edit', [
            'pageTitle' => 'تنظیمات عمومی سایت',
            'pageIcon' => 'fa-sliders',
            'pageDescription' => 'مدیریت هویت سایت، اطلاعات تماس، وضعیت سایت، شبکه‌های اجتماعی و مدل درآمدی.',
            'pageScope' => 'general',
            'groups' => $this->generalGroups(),
            'values' => Config::pluck('value', 'title')->all(),
            'submitLabel' => 'ذخیره تمام تنظیمات',
        ]);
    }

    public function payment()
    {
        abort_unless(auth()->user()?->hasPermissionBySlug('settings-manage'), 403);

        return view('admin.settings.edit', [
            'pageTitle' => 'تنظیمات پرداخت',
            'pageIcon' => 'fa-credit-card',
            'pageDescription' => 'مدیریت درگاه، کمیسیون، حداقل برداشت و قوانین تسویه میزبانان.',
            'pageScope' => 'payment',
            'groups' => $this->paymentGroups(),
            'values' => Config::pluck('value', 'title')->all(),
            'submitLabel' => 'ذخیره تنظیمات پرداخت',
        ]);
    }

    public function sms()
    {
        abort_unless(auth()->user()?->hasPermissionBySlug('settings-manage'), 403);

        return view('admin.settings.edit', [
            'pageTitle' => 'تنظیمات پیامک',
            'pageIcon' => 'fa-commenting',
            'pageDescription' => 'تنظیم اتصال سرویس پیامک و متن‌های پایه ارسال.',
            'pageScope' => 'sms',
            'groups' => $this->smsGroups(),
            'values' => Config::pluck('value', 'title')->all(),
            'submitLabel' => 'ذخیره تنظیمات پیامک',
        ]);
    }

    public function seo()
    {
        abort_unless(auth()->user()?->hasPermissionBySlug('settings-manage'), 403);

        return view('admin.settings.edit', [
            'pageTitle' => 'تنظیمات SEO',
            'pageIcon' => 'fa-search',
            'pageDescription' => 'مدیریت عنوان، متا، کلمات کلیدی، favicon و لوگوی سایت.',
            'pageScope' => 'seo',
            'groups' => $this->seoGroups(),
            'values' => Config::pluck('value', 'title')->all(),
            'submitLabel' => 'ذخیره تنظیمات SEO',
        ]);
    }

    public function seasonalBanners()
    {
        abort_unless(auth()->user()?->hasPermissionBySlug('settings-manage'), 403);

        return view('admin.settings.edit', [
            'pageTitle' => 'بنرهای فصلی',
            'pageIcon' => 'fa-leaf',
            'pageDescription' => 'مدیریت تصویر، عنوان و توضیح مستقل برای بهار، تابستان، پاییز و زمستان.',
            'pageScope' => 'seasonal',
            'groups' => $this->seasonalBannerGroups(),
            'values' => Config::pluck('value', 'title')->all(),
            'submitLabel' => 'ذخیره بنرهای فصلی',
        ]);
    }

    public function update(SettingsRequest $request)
    {
        abort_unless(auth()->user()?->hasPermissionBySlug('settings-manage'), 403);

        foreach ($request->validated('settings', []) as $key => $value) {
            if ($value instanceof UploadedFile) {
                $previousValue = Config::query()->where('title', $key)->value('value');
                $value = AdminFileManager::store($value, 'settings');
                AdminFileManager::delete($previousValue);
            }

            Config::updateOrCreate(
                ['title' => $key],
                ['value' => $value]
            );
        }

        app(ActivityLogService::class)->log('settings_update', SettingsController::class, $request, description: 'بروزرسانی تنظیمات سایت');

        if ($request->boolean('send_test_sms')) {
            $this->sendTestSms();

            return back()->with('admin_success', 'تنظیمات پیامک ذخیره شد و پیامک تست ثبت شد.');
        }

        return back()->with('admin_success', 'تنظیمات سایت بروزرسانی شد.');
    }

    protected function sendTestSms(): void
    {
        $phone = auth()->user()?->phone ?: Config::query()->where('title', 'support-phone')->value('value');

        if (blank($phone)) {
            return;
        }

        $smsLog = SmsLog::query()->create([
            'created_by' => auth()->id(),
            'phone' => $phone,
            'message' => Config::query()->where('title', 'sms_welcome_text')->value('value') ?: 'پیامک تست تنظیمات سایت',
            'provider' => Config::query()->where('title', 'sms_service')->value('value') ?: 'log',
            'status' => 'draft',
        ]);

        app(SmsService::class)->send($smsLog);
    }

    protected function generalGroups(): array
    {
        return [
            'اطلاعات اصلی' => [
                'website-title' => ['label' => 'نام سایت', 'required' => true],
                'website-titleEn' => ['label' => 'شعار سایت'],
                'website-description' => ['label' => 'توضیحات سایت', 'type' => 'textarea'],
                'support-phone' => ['label' => 'شماره تماس پشتیبانی'],
                'support-phone-secondary' => ['label' => 'شماره تماس دوم'],
                'support-email' => ['label' => 'ایمیل پشتیبانی', 'type' => 'email'],
                'office-address' => ['label' => 'آدرس دفتر مرکزی', 'type' => 'textarea'],
                'mainBannerText' => ['label' => 'متن بنر تبلیغاتی بالای صفحه', 'type' => 'textarea'],
            ],
            'وضعیت و مدل درآمدی' => [
                'websiteStatus' => ['label' => 'وضعیت سایت', 'type' => 'select', 'options' => ['1' => 'فعال', '0' => 'غیرفعال']],
                'OfflineModeText' => ['label' => 'پیام غیرفعال بودن سایت', 'type' => 'textarea'],
                'site_revenue_mode' => ['label' => 'مدل درآمدی سایت', 'type' => 'select', 'options' => AdminSiteSettings::revenueModeOptions()],
            ],
            'برندینگ و ظاهر' => [
                'mainColor' => ['label' => 'رنگ اصلی'],
                'secondaryColor' => ['label' => 'رنگ دوم'],
                'website-icon' => ['label' => 'لوگوی سایت', 'type' => 'file'],
                'default-site-image' => ['label' => 'تصویر پیش‌فرض سایت', 'type' => 'file'],
                'offlineModeIcon' => ['label' => 'تصویر حالت تعمیرات', 'type' => 'file'],
            ],
            'شبکه‌های اجتماعی' => [
                'instagram' => ['label' => 'اینستاگرام', 'type' => 'url'],
                'telegram' => ['label' => 'تلگرام', 'type' => 'url'],
                'whatsapp' => ['label' => 'واتساپ', 'type' => 'url'],
                'linkedin' => ['label' => 'لینکدین', 'type' => 'url'],
            ],
        ];
    }

    protected function paymentGroups(): array
    {
        return [
            'درگاه پرداخت' => [
                'payment_gateway' => ['label' => 'درگاه پرداخت اصلی', 'type' => 'select', 'options' => ['zarinpal' => 'زرین‌پال', 'idpay' => 'آی‌دی‌پی', 'manual' => 'ثبت دستی']],
                'payment_merchant_code' => ['label' => 'مرچنت کد'],
                'payment_api_key' => ['label' => 'API Key'],
            ],
            'کمیسیون و برداشت میزبان' => [
                'payment_commission_percent' => ['label' => 'درصد کمیسیون سایت', 'type' => 'number'],
                'payment_min_withdraw_amount' => ['label' => 'حداقل مبلغ برداشت میزبان', 'type' => 'number'],
                'payment_daily_withdraw_limit' => ['label' => 'حداکثر مبلغ برداشت روزانه', 'type' => 'number'],
            ],
        ];
    }

    protected function smsGroups(): array
    {
        return [
            'اتصال سرویس پیامک' => [
                'sms_service' => ['label' => 'ارائه‌دهنده پیامک', 'type' => 'select', 'options' => ['farazsms' => 'فراز اس‌ام‌اس', 'ippanel' => 'IPPanel', 'kavenegar' => 'کاوه‌نگار', 'log' => 'ثبت در لاگ']],
                'sms_sender_line' => ['label' => 'شماره خط ارسال‌کننده'],
                'sms_username' => ['label' => 'نام کاربری'],
                'sms_password' => ['label' => 'رمز عبور'],
                'sms_welcome_text' => ['label' => 'متن پیامک خوش‌آمدگویی', 'type' => 'textarea'],
            ],
        ];
    }

    protected function seoGroups(): array
    {
        return [
            'تنظیمات پیش‌فرض' => [
                'website-title' => ['label' => 'عنوان', 'required' => true],
                'website-description' => ['label' => 'توضیحات متا', 'type' => 'textarea', 'required' => true],
                'website-words' => ['label' => 'کلمات کلیدی', 'type' => 'textarea', 'required' => true],
                'seo_favicon' => ['label' => 'Favicon', 'type' => 'file', 'required' => true],
                'website-icon' => ['label' => 'لوگو', 'type' => 'file', 'required' => true],
            ],
        ];
    }

    protected function seasonalBannerGroups(): array
    {
        return [
            'بهار' => [
                'season_spring_title' => ['label' => 'عنوان بهار'],
                'season_spring_description' => ['label' => 'توضیحات بهار', 'type' => 'textarea'],
                'season_spring_image' => ['label' => 'تصویر بهار', 'type' => 'file'],
            ],
            'تابستان' => [
                'season_summer_title' => ['label' => 'عنوان تابستان'],
                'season_summer_description' => ['label' => 'توضیحات تابستان', 'type' => 'textarea'],
                'season_summer_image' => ['label' => 'تصویر تابستان', 'type' => 'file'],
            ],
            'پاییز' => [
                'season_autumn_title' => ['label' => 'عنوان پاییز'],
                'season_autumn_description' => ['label' => 'توضیحات پاییز', 'type' => 'textarea'],
                'season_autumn_image' => ['label' => 'تصویر پاییز', 'type' => 'file'],
            ],
            'زمستان' => [
                'season_winter_title' => ['label' => 'عنوان زمستان'],
                'season_winter_description' => ['label' => 'توضیحات زمستان', 'type' => 'textarea'],
                'season_winter_image' => ['label' => 'تصویر زمستان', 'type' => 'file'],
            ],
        ];
    }
}
