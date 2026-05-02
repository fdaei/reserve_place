<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminLoginRequest;
use App\Http\Requests\Admin\AdminVerifyLoginRequest;
use App\Services\Admin\ActivityLogService;
use App\Services\Admin\SecurityEventService;
use App\Models\User;
use App\Models\VerificationCode;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    public function showLogin()
    {
        if (auth()->check() && $this->hasAdminAccess(auth()->user())) {
            return $this->redirectToFirstAccessibleAdminRoute(auth()->user());
        }

        $phone = session('admin_login_phone') ?: old('phone');

        return view('admin.auth.login', [
            'phone' => $phone,
            'codeSent' => session()->has('admin_login_phone') || old('code') !== null,
        ]);
    }

    public function sendCode(AdminLoginRequest $request)
    {
        $phone = convertPersianToEnglishNumbers($request->validated('phone'));
        $user = User::where('phone', $phone)->first();

        if (! $user || ! $this->hasAdminAccess($user)) {
            app(SecurityEventService::class)->log('admin_login_user_not_found', $request, ['phone' => $phone], $user, 'warning');

            return back()->withErrors([
                'phone' => 'حساب مدیریتی با این شماره پیدا نشد.',
            ])->withInput();
        }

        $code = (string) config('admin.demo_code', env('ADMIN_DEMO_CODE', '1111'));

        VerificationCode::query()
            ->where('phone', $phone)
            ->where('is_use', false)
            ->update(['is_use' => true]);

        VerificationCode::create([
            'phone' => $phone,
            'code' => $code,
            'is_use' => false,
        ]);

        return redirect()
            ->route('admin.login')
            ->with('admin_login_phone', $phone)
            ->with('admin_demo_code', app()->environment('production') ? null : $code)
            ->with('admin_success', 'کد تایید برای ورود به پنل مدیریت آماده شد.');
    }

    public function verify(AdminVerifyLoginRequest $request)
    {
        $phone = convertPersianToEnglishNumbers($request->validated('phone'));
        $code = convertPersianToEnglishNumbers($request->validated('code'));

        $verification = VerificationCode::query()
            ->where('phone', $phone)
            ->where('code', $code)
            ->where('is_use', false)
            ->where('created_at', '>=', now()->subMinutes(5))
            ->latest('id')
            ->first();

        if (! $verification) {
            app(SecurityEventService::class)->log('admin_login_invalid_code', $request, ['phone' => $phone], null, 'warning');

            return back()
                ->withErrors(['code' => 'کد تایید وارد شده صحیح نیست.'])
                ->with('admin_login_phone', $phone);
        }

        $user = User::where('phone', $phone)->first();

        if (! $user || ! $this->hasAdminAccess($user)) {
            app(SecurityEventService::class)->log('admin_login_access_revoked', $request, ['phone' => $phone], $user, 'warning');

            return back()->withErrors([
                'phone' => 'حساب شما دسترسی ورود به پنل مدیریت را ندارد.',
            ]);
        }

        $verification->update(['is_use' => true]);

        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->forget('admin_login_phone');
        $user->forceFill(['last_seen_at' => now()])->saveQuietly();

        app(ActivityLogService::class)->log('login', $user, $request, description: 'ورود به پنل مدیریت');
        app(SecurityEventService::class)->log('admin_login_success', $request, ['phone' => $phone], $user);

        return $this->redirectToFirstAccessibleAdminRoute($user)
            ->with('admin_success', 'با موفقیت وارد پنل مدیریت شدید.');
    }

    public function logout()
    {
        $request = request();

        if ($user = auth()->user()) {
            app(ActivityLogService::class)->log('logout', $user, $request, description: 'خروج از پنل مدیریت');
            app(SecurityEventService::class)->log('admin_logout', $request, [], $user);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    protected function hasAdminAccess(User $user): bool
    {
        return $user->canAccessAdminPanel();
    }

    protected function redirectToFirstAccessibleAdminRoute(User $user)
    {
        if ($user->hasPermissionBySlug('dashboard-view')) {
            return redirect()->route('admin.dashboard');
        }

        foreach ($this->adminLandingRoutes() as $permission => [$route, $parameters]) {
            if ($user->hasPermissionBySlug($permission)) {
                return redirect()->route($route, $parameters);
            }
        }

        return redirect()->route('admin.dashboard');
    }

    protected function adminLandingRoutes(): array
    {
        return [
            'users-manage' => ['admin.resources.index', ['users']],
            'roles-manage' => ['admin.resources.index', ['roles']],
            'permissions-manage' => ['admin.resources.index', ['permissions']],
            'residences-manage' => ['admin.resources.index', ['residences']],
            'tours-manage' => ['admin.resources.index', ['tours']],
            'restaurants-manage' => ['admin.resources.index', ['restaurants']],
            'travel-partners-manage' => ['admin.resources.index', ['travel-partners']],
            'pages-manage' => ['admin.resources.index', ['pages']],
            config('access-control.content_manage_permission') => ['admin.locations.index', []],
            'bookings-manage' => ['admin.booking-requests.index', []],
            'finance-manage' => ['admin.host-wallet.index', []],
            'tickets-manage' => ['admin.tickets.index', []],
            'notifications-manage' => ['admin.resources.index', ['notifications']],
            'sms-manage' => ['admin.resources.index', ['sms-templates']],
            'settings-manage' => ['admin.settings.edit', []],
            'exports-manage' => ['admin.export.index', []],
            'activity-logs-view' => ['admin.resources.index', ['activity-logs']],
            'security-view' => ['admin.resources.index', ['security-events']],
        ];
    }
}
