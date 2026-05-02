<?php

namespace App\Providers;

use App\Models\User;
use App\Services\Sms\LogSmsProvider;
use App\Services\Sms\NullSmsProvider;
use App\Services\Sms\SmsProviderInterface;
use App\Support\Admin\AdminSiteSettings;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SmsProviderInterface::class, function () {
            return match (config('services.sms.driver', 'null')) {
                'log' => new LogSmsProvider(),
                default => new NullSmsProvider(),
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        Blade::directive('maskPhoneNumber', function ($expression) {
            return "<?php echo maskPhoneNumber($expression); ?>";
        });

        View::composer(['layouts.admin', 'admin.*'], function ($view) {
            $onlineEmployees = collect();

            if (
                Schema::hasTable('users')
                && Schema::hasTable('roles')
                && Schema::hasTable('role_user')
                && Schema::hasColumn('users', 'last_seen_at')
            ) {
                $onlineEmployees = User::query()
                    ->employees()
                    ->online(5)
                    ->with('roles')
                    ->latest('last_seen_at')
                    ->limit(8)
                    ->get();
            }

            $view->with('onlineEmployees', $onlineEmployees)
                ->with('onlineEmployeesCount', $onlineEmployees->count())
                ->with('siteRevenueMode', AdminSiteSettings::revenueMode())
                ->with('siteRevenueModeLabel', AdminSiteSettings::revenueModeLabel())
                ->with('siteRevenueModeIcon', AdminSiteSettings::revenueModeIcon());
        });
    }
}
