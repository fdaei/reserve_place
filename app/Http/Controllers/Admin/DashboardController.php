<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingRequest;
use App\Models\CallResidences;
use App\Models\Commission;
use App\Models\FoodStore;
use App\Models\Friend;
use App\Models\HostWalletTransaction;
use App\Models\WithdrawRequest;
use App\Models\Residence;
use App\Models\Ticket;
use App\Models\Tour;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __invoke()
    {
        abort_unless(auth()->user()?->hasPermissionBySlug('dashboard-view'), 403);

        $today = Carbon::today();
        $onlineEmployees = User::query()
            ->employees()
            ->online(5)
            ->with('roles')
            ->latest('last_seen_at')
            ->limit(12)
            ->get();

        $stats = [
            [
                'title' => 'تعداد اقامتگاه‌ها',
                'value' => Residence::count(),
                'meta' => 'در انتظار تأیید: '.Residence::where('status', 0)->count(),
                'icon' => 'fa-building',
                'tone' => 'blue',
            ],
            [
                'title' => 'درخواست‌های امروز',
                'value' => BookingRequest::whereDate('created_at', $today)->count(),
                'meta' => 'کل درخواست‌ها: '.BookingRequest::count(),
                'icon' => 'fa-phone',
                'tone' => 'amber',
            ],
            [
                'title' => 'کیف پول میزبانان',
                'value' => $this->walletBalance(),
                'meta' => 'موجودی کل به تومان',
                'icon' => 'fa-money',
                'tone' => 'green',
            ],
            [
                'title' => 'کمیسیون این ماه',
                'value' => Commission::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('amount'),
                'meta' => 'تومان',
                'icon' => 'fa-percent',
                'tone' => 'rose',
            ],
            [
                'title' => 'منتظر تأیید میزبان',
                'value' => BookingRequest::where('status', 'pending')->count(),
                'meta' => 'درخواست رزرو',
                'icon' => 'fa-clock-o',
                'tone' => 'sky',
            ],
            [
                'title' => 'منتظر پرداخت',
                'value' => BookingRequest::where('status', 'approved')->where('payment_status', 'unpaid')->count(),
                'meta' => 'پس از تأیید میزبان',
                'icon' => 'fa-credit-card',
                'tone' => 'amber',
            ],
            [
                'title' => 'رزروهای تکمیل‌شده',
                'value' => Booking::whereIn('status', ['completed', 'settled'])->count(),
                'meta' => 'پایان اقامت یا تسویه',
                'icon' => 'fa-calendar-check-o',
                'tone' => 'blue',
            ],
            [
                'title' => 'موجودی قابل برداشت',
                'value' => $this->availableWalletBalance(),
                'meta' => 'تومان',
                'icon' => 'fa-check-circle',
                'tone' => 'green',
            ],
            [
                'title' => 'برداشت در انتظار',
                'value' => WithdrawRequest::where('status', 'pending')->count(),
                'meta' => 'نیازمند بررسی مالی',
                'icon' => 'fa-bank',
                'tone' => 'rose',
            ],
        ];

        $dailyRequests = collect(range(29, 0))
            ->map(function (int $daysAgo) {
                $date = now()->subDays($daysAgo)->startOfDay();

                return [
                    'label' => \App\Support\Admin\PersianDate::formatForDisplay($date) ?: $date->toDateString(),
                    'count' => BookingRequest::whereDate('created_at', $date)->count(),
                ];
            });

        return view('admin.dashboard', [
            'stats' => $stats,
            'requestStatusStats' => [
                'pending' => BookingRequest::where('status', 'pending')->count(),
                'approved' => BookingRequest::where('status', 'approved')->where('payment_status', 'unpaid')->count(),
                'paid' => BookingRequest::where('payment_status', 'paid')->count(),
                'releasable' => BookingRequest::where('settlement_status', 'releasable')->count(),
                'settled' => BookingRequest::where('settlement_status', 'settled')->count(),
                'cancelled' => BookingRequest::whereIn('status', ['cancelled', 'rejected'])->count(),
            ],
            'dailyRequests' => $dailyRequests,
            'recentRequests' => BookingRequest::with(['customer', 'host', 'bookable'])->latest('id')->limit(8)->get(),
            'recentTransactions' => HostWalletTransaction::with('host')->latest('id')->limit(6)->get(),
            'onlineEmployees' => $onlineEmployees,
            'callStats' => [
                'residence' => CallResidences::where('type', 'residence')->count(),
                'tour' => CallResidences::where('type', 'tour')->count(),
                'friend' => CallResidences::where('type', 'friend')->count(),
                'store' => CallResidences::where('type', 'store')->count(),
            ],
        ]);
    }

    private function walletBalance(): int
    {
        $credit = HostWalletTransaction::where('type', 'credit')->where('status', '!=', 'cancelled')->sum('amount');
        $debit = HostWalletTransaction::where('type', 'debit')->where('status', '!=', 'cancelled')->sum('amount');

        return (int) $credit - (int) $debit;
    }

    private function availableWalletBalance(): int
    {
        $credit = HostWalletTransaction::where('type', 'credit')->whereIn('status', ['posted', 'available'])->sum('amount');
        $debit = HostWalletTransaction::where('type', 'debit')->where('status', 'posted')->sum('amount');

        return (int) $credit - (int) $debit;
    }
}
