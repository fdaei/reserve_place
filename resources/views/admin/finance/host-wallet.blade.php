@extends('layouts.admin')

@section('title', 'کیف پول میزبانان')

@section('content')
    <x-admin.page-shell title="کیف پول میزبانان" icon="fa-money" description="نمای میزبان‌محور از موجودی، مبالغ مسدود و درخواست‌های برداشت.">
        <div class="stats-grid">
            <x-admin.stats-card title="کل موجودی میزبانان" :value="number_format($stats['total']) . ' تومان'" meta="اعتبار منهای برداشت‌ها" icon="fa-money" tone="blue" />
            <x-admin.stats-card title="موجودی مسدود شده" :value="number_format($stats['blocked']) . ' تومان'" meta="تا پایان اقامت" icon="fa-lock" tone="amber" />
            <x-admin.stats-card title="موجودی قابل برداشت" :value="number_format($stats['available']) . ' تومان'" meta="آماده درخواست برداشت" icon="fa-check-circle" tone="green" />
            <x-admin.stats-card title="درخواست برداشت در انتظار" :value="number_format($stats['pendingWithdraws'])" meta="نیازمند بررسی مالی" icon="fa-bank" tone="rose" />
        </div>

        <div class="admin-subsection">
            <div class="listing-table-wrap">
                <table class="table listing-table">
                    <thead>
                    <tr>
                        <th>نام میزبان</th>
                        <th>تعداد اقامتگاه‌ها</th>
                        <th>موجودی کل</th>
                        <th>موجودی مسدود شده</th>
                        <th>موجودی قابل برداشت</th>
                        <th>درخواست برداشت فعال</th>
                        <th>عملیات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($hosts as $host)
                        @php
                            $credits = $host->walletTransactions->where('type', 'credit')->where('status', '!=', 'cancelled')->sum('amount');
                            $debits = $host->walletTransactions->where('type', 'debit')->where('status', '!=', 'cancelled')->sum('amount');
                            $blocked = $host->walletTransactions->where('type', 'credit')->where('status', 'blocked')->sum('amount');
                            $available = $host->walletTransactions->where('type', 'credit')->whereIn('status', ['posted', 'available'])->sum('amount') - $host->walletTransactions->where('type', 'debit')->where('status', 'posted')->sum('amount');
                        @endphp
                        <tr>
                            <td>{{ $host->full_name }}</td>
                            <td>{{ number_format($host->residences_count) }}</td>
                            <td>{{ number_format($credits - $debits) }} تومان</td>
                            <td>{{ number_format($blocked) }} تومان</td>
                            <td>{{ number_format($available) }} تومان</td>
                            <td>{{ number_format($host->active_withdraw_requests_count) }}</td>
                            <td>
                                <div class="listing-actions">
                                    <a href="{{ route('admin.resources.index', 'wallet-transactions') }}?search={{ urlencode($host->phone) }}" class="listing-action-btn listing-action-btn--info">تراکنش‌ها</a>
                                    <a href="{{ route('admin.resources.create', 'wallet-transactions') }}?host_id={{ $host->id }}" class="listing-action-btn listing-action-btn--success">شارژ کیف پول</a>
                                    <a href="{{ route('admin.resources.create', 'settlements') }}?host_id={{ $host->id }}" class="listing-action-btn listing-action-btn--dark">تسویه</a>
                                    <a href="{{ route('admin.resources.index', 'withdraw-requests') }}?search={{ urlencode($host->phone) }}" class="listing-action-btn listing-action-btn--warning">درخواست‌ها</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="listing-pagination">
                {{ $hosts->links('vendor.pagination.bootstrap-4') }}
            </div>
        </div>

        <div class="finance-rules">
            <strong>قوانین مالی</strong>
            <span>مبلغ رزرو تا پایان دوره اقامت در کیف پول میزبان مسدود می‌شود.</span>
            <span>پس از پایان اقامت، مبلغ به کیف پول قابل برداشت اضافه می‌شود.</span>
            <span>حداقل مبلغ برداشت طبق تنظیمات پرداخت تعیین می‌شود.</span>
            <span>تسویه حساب‌ها طی ۴۸ ساعت کاری انجام می‌شود.</span>
            <span>کمیسیون سایت قبل از واریز به کیف پول میزبان کسر می‌شود.</span>
        </div>
    </x-admin.page-shell>
@endsection
