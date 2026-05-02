@extends('layouts.admin')

@section('title', 'کمیسیون‌ها')

@section('content')
    <x-admin.page-shell title="کمیسیون‌ها" icon="fa-percent" description="گزارش کمیسیون‌های ثبت‌شده از رزروها و وضعیت تسویه آن‌ها.">
        <div class="stats-grid">
            <x-admin.stats-card title="کمیسیون امروز" :value="number_format($stats['today']) . ' تومان'" meta="رزروهای امروز" icon="fa-calendar" tone="green" />
            <x-admin.stats-card title="کمیسیون این ماه" :value="number_format($stats['month']) . ' تومان'" meta="ماه جاری" icon="fa-calendar-check-o" tone="blue" />
            <x-admin.stats-card title="کل کمیسیون" :value="number_format($stats['total']) . ' تومان'" meta="تمام دوره‌ها" icon="fa-percent" tone="amber" />
            <x-admin.stats-card title="کمیسیون تسویه‌شده" :value="number_format($stats['settled']) . ' تومان'" meta="ثبت‌شده به عنوان تسویه" icon="fa-check" tone="violet" />
        </div>

        <div class="admin-subsection">
            <form method="GET" action="{{ route('admin.commissions.index') }}" class="listing-toolbar">
                <div class="listing-toolbar-main">
                    <input type="text" name="search" value="{{ request('search') }}" class="listing-search" placeholder="جستجوی شماره رزرو یا میزبان">
                    <select name="status">
                        <option value="">همه وضعیت‌ها</option>
                        <option value="pending" @selected(request('status') === 'pending')>در انتظار</option>
                        <option value="settled" @selected(request('status') === 'settled')>تسویه شده</option>
                        <option value="cancelled" @selected(request('status') === 'cancelled')>لغو شده</option>
                    </select>
                    <select name="sort">
                        <option value="latest" @selected(request('sort', 'latest') === 'latest')>جدیدترین</option>
                        <option value="oldest" @selected(request('sort') === 'oldest')>قدیمی‌ترین</option>
                    </select>
                </div>
                <div class="listing-toolbar-actions">
                    <button type="submit" class="toolbar-btn toolbar-btn--dark">فیلتر</button>
                    <a href="{{ route('admin.commissions.index') }}" class="toolbar-btn toolbar-btn--light">پاک‌سازی</a>
                </div>
            </form>

            <div class="listing-table-wrap">
                <table class="table listing-table">
                    <thead>
                    <tr>
                        <th>شماره رزرو</th>
                        <th>اقامتگاه</th>
                        <th>میزبان</th>
                        <th>مبلغ کل رزرو</th>
                        <th>درصد کمیسیون</th>
                        <th>مبلغ کمیسیون</th>
                        <th>سهم میزبان</th>
                        <th>تاریخ</th>
                        <th>وضعیت پرداخت</th>
                        <th>وضعیت تسویه</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($commissions as $commission)
                        <tr>
                            <td>{{ $commission->booking?->booking_number ?: '-' }}</td>
                            <td>{{ data_get($commission, 'booking.bookable.title') ?: '-' }}</td>
                            <td>{{ $commission->host?->full_name ?: '-' }}</td>
                            <td>{{ number_format((int) data_get($commission, 'booking.total_amount', 0)) }} تومان</td>
                            <td>{{ rtrim(rtrim((string) $commission->rate, '0'), '.') }}٪</td>
                            <td>{{ number_format((int) $commission->amount) }} تومان</td>
                            <td>{{ number_format((int) $commission->host_share_amount) }} تومان</td>
                            <td>{{ \App\Support\Admin\AdminResourceRegistry::displayValue($commission, ['key' => 'created_at', 'type' => 'date']) }}</td>
                            <td><x-admin.status-badge :value="data_get($commission, 'booking.payment_status')" /></td>
                            <td><x-admin.status-badge :value="$commission->status" /></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="listing-pagination">
                {{ $commissions->links('vendor.pagination.bootstrap-4') }}
            </div>
        </div>
    </x-admin.page-shell>
@endsection
