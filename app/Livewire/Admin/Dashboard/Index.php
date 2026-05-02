<?php

namespace App\Livewire\Admin\Dashboard;

use App\Models\Booking;
use App\Models\BookingRequest;
use App\Models\CallResidences;
use App\Models\Commission;
use App\Models\HostWalletTransaction;
use App\Models\Residence;
use App\Models\User;
use App\Models\WithdrawRequest;
use Carbon\Carbon;
use Livewire\Component;

class Index extends Component
{
    public function mount(): void
    {
        abort_unless(auth()->user()?->hasPermissionBySlug('dashboard-view'), 403);
    }

    public function render()
    {
        $today = Carbon::today();

        return view('livewire.admin.dashboard.index', [
            'stats' => $this->stats($today),
            'requestStatusStats' => [
                'pending' => BookingRequest::where('status', 'pending')->count(),
                'approved' => BookingRequest::where('status', 'approved')->where('payment_status', 'unpaid')->count(),
                'paid' => BookingRequest::where('payment_status', 'paid')->count(),
                'releasable' => BookingRequest::where('settlement_status', 'releasable')->count(),
                'settled' => BookingRequest::where('settlement_status', 'settled')->count(),
                'cancelled' => BookingRequest::whereIn('status', ['cancelled', 'rejected'])->count(),
            ],
            'dailyRequests' => $this->dailyRequests(),
            'recentRequests' => BookingRequest::with(['customer', 'host', 'bookable'])->latest('id')->limit(8)->get(),
            'recentTransactions' => HostWalletTransaction::with('host')->latest('id')->limit(6)->get(),
            'callStats' => [
                'residence' => CallResidences::where('type', 'residence')->count(),
                'tour' => CallResidences::where('type', 'tour')->count(),
                'friend' => CallResidences::where('type', 'friend')->count(),
                'store' => CallResidences::where('type', 'store')->count(),
            ],
        ])
            ->extends('app')
            ->section('content');
    }

    protected function stats(Carbon $today): array
    {
        return [
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
    }

    protected function dailyRequests()
    {
        return collect(range(29, 0))
            ->map(function (int $daysAgo) {
                $date = now()->subDays($daysAgo)->startOfDay();

                return [
                    'label' => \App\Support\Admin\PersianDate::formatForDisplay($date) ?: $date->toDateString(),
                    'count' => BookingRequest::whereDate('created_at', $date)->count(),
                ];
            });
    }

    protected function walletBalance(): int
    {
        $credit = HostWalletTransaction::where('type', 'credit')->where('status', '!=', 'cancelled')->sum('amount');
        $debit = HostWalletTransaction::where('type', 'debit')->where('status', '!=', 'cancelled')->sum('amount');

        return (int) $credit - (int) $debit;
    }

    protected function availableWalletBalance(): int
    {
        $credit = HostWalletTransaction::where('type', 'credit')->whereIn('status', ['posted', 'available'])->sum('amount');
        $debit = HostWalletTransaction::where('type', 'debit')->where('status', 'posted')->sum('amount');

        return (int) $credit - (int) $debit;
    }
}
