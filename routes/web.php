<?php

use App\Models\Config;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminResourceController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\Admin\FooterSettingsController;
use App\Http\Controllers\Admin\RoleAssignmentController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\TicketController;
use App\Livewire\Admin\Bookings\BookingRequests as BookingRequestsPage;
use App\Livewire\Admin\Bookings as BookingsPage;
use App\Livewire\Admin\Content\Locations as LocationsPage;
use App\Livewire\Admin\Dashboard\Index as DashboardPage;
use App\Livewire\Admin\Finance\HostWallet as HostWalletPage;
use App\Livewire\Admin\Finance\Settlements as SettlementsPage;
use App\Livewire\Admin\Settings\SeasonalBanners as SeasonalBannersPage;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/



Route::get('/', \App\Livewire\User\Residence\Index::class);
Route::get('/contact', \App\Livewire\User\ContactUs::class);
Route::get('/ticket/{id}', \App\Livewire\User\Tickets\Tickets::class);
Route::get('/detail/{id}', \App\Livewire\User\Residence\Detail::class);
Route::get('/dashboard', \App\Livewire\User\Dashboard::class);
Route::get('/add-residence', \App\Livewire\User\Residence\AddResidence::class);
Route::get('/edit-residence/{id}', \App\Livewire\User\Residence\AddResidence::class);
Route::get('/profile', \App\Livewire\User\Profile::class);
Route::get('/login', \App\Livewire\User\Login::class);


Route::get("/linkedstorage", function () {
    $res = \Illuminate\Support\Facades\Artisan::call("storage:link");
    return $res;
});
Route::get("/lastWeekAm", function () {
    foreach (\App\Models\Residence::all() as $item){
        $item->last_week_amount=$item->amount;
        $item->update();
    }
    foreach (\App\Models\User::all() as $item){
        $item->phone=convertPersianToEnglishNumbers($item->phone);
        $item->national_code=convertPersianToEnglishNumbers($item->national_code);
        $item->update();
    }
});
Route::get('/p/{urlTitle}', \App\Livewire\User\Pages::class);

Route::get('/add-tour', \App\Livewire\User\Tour\Add::class);
Route::get('/edit-tour/{id}', \App\Livewire\User\Tour\Add::class);
Route::get('/tours', \App\Livewire\User\Tour\Index::class);
Route::get('/tour/{id}', \App\Livewire\User\Tour\Detail::class);

Route::get('/add-friend', \App\Livewire\User\Friend\Add::class);
Route::get('/edit-friend/{id}', \App\Livewire\User\Friend\Add::class);
Route::get('/friends', \App\Livewire\User\Friend\Index::class);
Route::get('/friend/{id}', \App\Livewire\User\Friend\Detail::class);

Route::get('/add-foodstore', \App\Livewire\User\FoodStore\Add::class);
Route::get('/edit-foodstore/{id}', \App\Livewire\User\FoodStore\Add::class);
Route::get('/stores', \App\Livewire\User\FoodStore\Index::class);
Route::get('/store/{id}', \App\Livewire\User\FoodStore\Detail::class);

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'sendCode'])->middleware('throttle:admin-auth')->name('login.send');
    Route::post('/login/verify', [AdminAuthController::class, 'verify'])->middleware('throttle:admin-auth')->name('login.verify');
    Route::get('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    Route::middleware(['auth', 'admin.access', 'permission:'.config('access-control.admin_login_permission'), 'throttle:admin-sensitive'])->group(function () {
        Route::redirect('/', '/admin/dashboard')->name('home');
        Route::get('/dashboard', DashboardPage::class)->name('dashboard');
        Route::redirect('/properties', '/admin/residences')->name('properties.index');
        Route::redirect('/pages/list', '/admin/pages')->name('pages.list');

        Route::get('/role-assign', [RoleAssignmentController::class, 'index'])->name('role-assign.index');
        Route::post('/role-assign', [RoleAssignmentController::class, 'store'])->name('role-assign.store');

        Route::get('/message', [TicketController::class, 'index'])->name('tickets.index');
        Route::get('/message/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
        Route::post('/message/{ticket}', [TicketController::class, 'reply'])->name('tickets.reply');
        Route::patch('/message/{ticket}/status', [TicketController::class, 'updateStatus'])->name('tickets.status');

        Route::get('/website-settings', [SettingsController::class, 'edit'])->name('settings.edit');
        Route::put('/website-settings', [SettingsController::class, 'update'])->name('settings.update');
        Route::get('/payment-settings', [SettingsController::class, 'payment'])->name('settings.payment');
        Route::put('/payment-settings', [SettingsController::class, 'update'])->name('settings.payment.update');
        Route::get('/sms-settings', [SettingsController::class, 'sms'])->name('settings.sms');
        Route::put('/sms-settings', [SettingsController::class, 'update'])->name('settings.sms.update');
        Route::get('/seo-settings', [SettingsController::class, 'seo'])->name('settings.seo');
        Route::put('/seo-settings', [SettingsController::class, 'update'])->name('settings.seo.update');
        Route::get('/seasonal-banners', SeasonalBannersPage::class)->name('seasonal-banners.edit');

        Route::get('/footer-links', [FooterSettingsController::class, 'index'])->name('footer-links.index');
        Route::put('/footer-links/texts', [FooterSettingsController::class, 'updateTexts'])->name('footer-links.texts');
        Route::post('/footer-links', [FooterSettingsController::class, 'store'])->name('footer-links.store');
        Route::patch('/footer-links/{footerLink}', [FooterSettingsController::class, 'update'])->name('footer-links.update');
        Route::delete('/footer-links/{footerLink}', [FooterSettingsController::class, 'destroy'])->name('footer-links.destroy');

        Route::get('/locations', LocationsPage::class)->name('locations.index');
        Route::get('/booking-requests', BookingRequestsPage::class)->name('booking-requests.index');
        Route::get('/bookings', BookingsPage::class)->name('bookings.index');
        Route::get('/host-wallet', HostWalletPage::class)->name('host-wallet.index');
        Route::get('/settlements', SettlementsPage::class)->name('settlements.index');
        Route::get('/commissions', [FinanceController::class, 'commissions'])->name('commissions.index');

        Route::get('/export', [ExportController::class, 'index'])->name('export.index');
        Route::post('/export', [ExportController::class, 'export'])->name('export.store');

        Route::get('/{resource}', [AdminResourceController::class, 'index'])->name('resources.index');
        Route::get('/{resource}/create', [AdminResourceController::class, 'create'])->name('resources.create');
        Route::post('/{resource}', [AdminResourceController::class, 'store'])->name('resources.store');
        Route::get('/{resource}/{id}', [AdminResourceController::class, 'show'])->whereNumber('id')->name('resources.show');
        Route::get('/{resource}/{id}/edit', [AdminResourceController::class, 'edit'])->whereNumber('id')->name('resources.edit');
        Route::match(['put', 'patch'], '/{resource}/{id}', [AdminResourceController::class, 'update'])->whereNumber('id')->name('resources.update');
        Route::delete('/{resource}/{id}', [AdminResourceController::class, 'destroy'])->whereNumber('id')->name('resources.destroy');
        Route::patch('/{resource}/{id}/status', [AdminResourceController::class, 'updateStatus'])->whereNumber('id')->name('resources.status');
    });
});
