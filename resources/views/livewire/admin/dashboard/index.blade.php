<div class="section listing-panel">
    <div class="listing-panel-head">
        <div>
            <h2 class="listing-panel-title">
                <span class="listing-panel-icon"><i class="fa fa-pie-chart"></i></span>
                داشبورد مدیریت
            </h2>
            <p class="admin-page-description">تمرکز اصلی روی اقامتگاه، درخواست رزرو، پرداخت، کیف پول میزبانان و تسویه.</p>
        </div>
    </div>

    <div class="stats-grid">
        @foreach($stats as $stat)
            <x-admin.stats-card
                :title="$stat['title']"
                :value="$stat['value']"
                :meta="$stat['meta']"
                :icon="$stat['icon']"
                :tone="$stat['tone']"
            />
        @endforeach
    </div>

    <div class="admin-dashboard-grid admin-dashboard-grid--wide">
        <div class="admin-dashboard-panel">
            <div class="admin-dashboard-panel-head">
                <h3><i class="fa fa-tasks"></i> وضعیت درخواست‌های رزرو</h3>
                <a href="{{ route('admin.resources.index', 'booking-requests') }}">مشاهده درخواست‌ها</a>
            </div>
            <div class="status-summary-grid">
                <div><span>در انتظار تأیید میزبان</span><strong>{{ number_format($requestStatusStats['pending']) }}</strong></div>
                <div><span>منتظر پرداخت</span><strong>{{ number_format($requestStatusStats['approved']) }}</strong></div>
                <div><span>پرداخت شده</span><strong>{{ number_format($requestStatusStats['paid']) }}</strong></div>
                <div><span>آماده تسویه</span><strong>{{ number_format($requestStatusStats['releasable']) }}</strong></div>
                <div><span>تسویه شده</span><strong>{{ number_format($requestStatusStats['settled']) }}</strong></div>
                <div><span>لغو / رد شده</span><strong>{{ number_format($requestStatusStats['cancelled']) }}</strong></div>
            </div>
        </div>

        <div class="admin-dashboard-panel">
            <div class="admin-dashboard-panel-head">
                <h3><i class="fa fa-line-chart"></i> درخواست‌های ۳۰ روز گذشته</h3>
            </div>
            @php($maxDaily = max(1, $dailyRequests->max('count')))
            <div class="mini-chart">
                @foreach($dailyRequests as $day)
                    <div class="mini-chart-bar" title="{{ $day['label'] }} - {{ number_format($day['count']) }} درخواست">
                        <span style="height: {{ max(8, ($day['count'] / $maxDaily) * 100) }}%"></span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="admin-dashboard-panel">
        <div class="admin-dashboard-panel-head">
            <h3><i class="fa fa-calendar-check-o"></i> آخرین درخواست‌های رزرو</h3>
            <a href="{{ route('admin.resources.index', 'booking-requests') }}">مشاهده همه</a>
        </div>
        <div class="listing-table-wrap">
            <table class="table listing-table">
                <thead>
                <tr>
                    <th>کاربر / مهمان</th>
                    <th>اقامتگاه</th>
                    <th>میزبان</th>
                    <th>تاریخ ورود</th>
                    <th>تاریخ خروج</th>
                    <th>مبلغ کل</th>
                    <th>وضعیت</th>
                    <th>عملیات</th>
                </tr>
                </thead>
                <tbody>
                @forelse($recentRequests as $request)
                    <tr>
                        <td>{{ $request->customer?->full_name ?: $request->guest_name }}</td>
                        <td>{{ data_get($request, 'bookable.title', 'اقامتگاه حذف شده') }}</td>
                        <td>{{ $request->host?->full_name ?: '-' }}</td>
                        <td>{{ \App\Support\Admin\AdminResourceRegistry::displayValue($request, ['key' => 'starts_at', 'type' => 'date']) }}</td>
                        <td>{{ \App\Support\Admin\AdminResourceRegistry::displayValue($request, ['key' => 'ends_at', 'type' => 'date']) }}</td>
                        <td>{{ number_format((int) $request->total_amount) }} تومان</td>
                        <td><x-admin.status-badge :value="$request->status" /></td>
                        <td><a href="{{ route('admin.resources.show', ['booking-requests', $request->id]) }}" class="listing-action-btn listing-action-btn--info">جزئیات</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <x-admin.empty-state title="درخواستی ثبت نشده" description="پس از ثبت درخواست رزرو، این جدول تکمیل می‌شود." />
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="admin-dashboard-panel">
        <div class="admin-dashboard-panel-head">
            <h3><i class="fa fa-money"></i> آخرین تراکنش‌ها</h3>
            <a href="{{ route('admin.resources.index', 'wallet-transactions') }}">همه تراکنش‌ها</a>
        </div>
        @forelse($recentTransactions as $transaction)
            <div class="admin-activity-row">
                <div>
                    <strong>{{ $transaction->host?->full_name ?: 'میزبان' }}</strong>
                    <span>{{ number_format((int) $transaction->amount) }} تومان - {{ $transaction->description ?: $transaction->reference_number }}</span>
                </div>
                <x-admin.status-badge :value="$transaction->status" />
            </div>
        @empty
            <x-admin.empty-state title="تراکنشی ثبت نشده" description="تراکنش‌های کیف پول پس از پرداخت یا برداشت نمایش داده می‌شوند." />
        @endforelse
    </div>
</div>
