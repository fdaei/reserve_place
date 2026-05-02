<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\SmsTemplate;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class AccessControlSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'مدیر کل', 'slug' => config('access-control.super_admin_role')],
            ['name' => 'مدیر مالی', 'slug' => 'finance-manager'],
            ['name' => 'اپراتور تماس', 'slug' => 'call-operator'],
            ['name' => 'مدیر محتوا', 'slug' => 'content-manager'],
            ['name' => 'کارمند', 'slug' => config('access-control.employee_role')],
            ['name' => 'میزبان', 'slug' => config('access-control.host_role')],
        ];

        $permissions = [
            ['name' => 'ورود به پنل مدیریت', 'slug' => config('access-control.admin_login_permission')],
            ['name' => 'مشاهده داشبورد', 'slug' => 'dashboard-view'],
            ['name' => 'مدیریت کاربران', 'slug' => 'users-manage'],
            ['name' => 'مدیریت نقش‌ها', 'slug' => 'roles-manage'],
            ['name' => 'مدیریت مجوزها', 'slug' => 'permissions-manage'],
            ['name' => 'مدیریت محتوا', 'slug' => config('access-control.content_manage_permission')],
            ['name' => 'مدیریت صفحات', 'slug' => 'pages-manage'],
            ['name' => 'مدیریت اقامتگاه‌ها', 'slug' => 'residences-manage'],
            ['name' => 'مدیریت تورها', 'slug' => 'tours-manage'],
            ['name' => 'مدیریت رستوران‌ها', 'slug' => 'restaurants-manage'],
            ['name' => 'مدیریت همسفر', 'slug' => 'travel-partners-manage'],
            ['name' => 'مدیریت رزروها', 'slug' => 'bookings-manage'],
            ['name' => 'مدیریت مالی', 'slug' => 'finance-manage'],
            ['name' => 'مدیریت تیکت‌ها', 'slug' => 'tickets-manage'],
            ['name' => 'مدیریت اعلان‌ها', 'slug' => 'notifications-manage'],
            ['name' => 'مدیریت پیامک', 'slug' => 'sms-manage'],
            ['name' => 'مشاهده لاگ فعالیت‌ها', 'slug' => 'activity-logs-view'],
            ['name' => 'مشاهده رخدادهای امنیتی', 'slug' => 'security-view'],
            ['name' => 'مدیریت تنظیمات', 'slug' => 'settings-manage'],
            ['name' => 'دریافت خروجی', 'slug' => 'exports-manage'],
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(['slug' => $roleData['slug']], $roleData);
        }

        foreach ($permissions as $permissionData) {
            Permission::updateOrCreate(['slug' => $permissionData['slug']], $permissionData);
        }

        $superAdminRole = Role::where('slug', config('access-control.super_admin_role'))->first();
        if ($superAdminRole) {
            $superAdminRole->permissions()->sync(Permission::pluck('id')->all());
        }

        $superAdminPhone = config('access-control.super_admin_phone');
        if ($superAdminPhone && $superAdminRole) {
            $user = User::where('phone', convertPersianToEnglishNumbers($superAdminPhone))->first();

            if ($user) {
                $user->roles()->syncWithoutDetaching([$superAdminRole->id]);
            }
        }

        if (Schema::hasTable('sms_templates')) {
            foreach ($this->defaultSmsTemplates() as $template) {
                SmsTemplate::updateOrCreate(['type' => $template['type']], $template);
            }
        }
    }

    private function defaultSmsTemplates(): array
    {
        return [
            ['title' => 'ثبت‌نام کاربر', 'type' => 'user_registered', 'body' => 'به {site} خوش آمدید.'],
            ['title' => 'ثبت درخواست رزرو', 'type' => 'booking_requested', 'body' => 'درخواست رزرو شما با کد {request_number} ثبت شد.'],
            ['title' => 'تأیید درخواست توسط میزبان', 'type' => 'host_approved', 'body' => 'درخواست رزرو {request_number} تأیید شد. لطفاً پرداخت را تکمیل کنید.'],
            ['title' => 'رد درخواست توسط میزبان', 'type' => 'host_rejected', 'body' => 'درخواست رزرو {request_number} توسط میزبان رد شد.'],
            ['title' => 'پرداخت موفق', 'type' => 'payment_success', 'body' => 'پرداخت رزرو {booking_number} با موفقیت انجام شد.'],
            ['title' => 'لغو رزرو', 'type' => 'booking_cancelled', 'body' => 'رزرو {booking_number} لغو شد.'],
            ['title' => 'پایان اقامت', 'type' => 'stay_ended', 'body' => 'اقامت رزرو {booking_number} پایان یافت.'],
            ['title' => 'قابل برداشت شدن مبلغ برای میزبان', 'type' => 'host_amount_releasable', 'body' => 'مبلغ رزرو {booking_number} برای شما قابل برداشت شد.'],
            ['title' => 'تأیید برداشت', 'type' => 'withdraw_approved', 'body' => 'درخواست برداشت شما تأیید و واریز شد.'],
            ['title' => 'رد برداشت', 'type' => 'withdraw_rejected', 'body' => 'درخواست برداشت شما رد شد.'],
        ];
    }
}
