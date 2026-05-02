@push('head')
    <style>
        .bookings-page {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .bookings-card {
            border-radius: 18px;
            border: 1px solid var(--admin-border);
            background: #fff;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
            overflow: hidden;
        }

        .bookings-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 18px 18px 8px;
        }

        .bookings-head h3 {
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 1rem;
            font-weight: 700;
            color: var(--admin-text);
        }

        .bookings-head h3 i {
            color: var(--admin-primary);
        }

        .bookings-filters {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr auto;
            gap: 8px;
            padding: 0 18px 14px;
        }

        .finance-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 24px;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 700;
        }

        .finance-badge.active {
            color: #047857;
            background: #d8f8e7;
        }

        .finance-badge.pending {
            color: #9a5b00;
            background: #fff1c2;
        }

        .finance-badge.danger {
            color: #b91c1c;
            background: #fee2e2;
        }

        .bookings-empty-state {
            padding: 14px 18px 20px;
        }

        @media (max-width: 900px) {
            .bookings-head,
            .bookings-filters {
                padding-right: 14px;
                padding-left: 14px;
            }

            .bookings-filters {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

<div class="section bookings-page">
    <div class="admin-section-head">
        <div>
            <h2>
                <i class="fa fa-calendar-check-o"></i>
                رزروها
            </h2>
        </div>
    </div>

    <div class="bookings-card">
        <div class="bookings-head">
            <h3>
                <i class="fa fa-calendar-check-o"></i>
                رزروها
            </h3>
        </div>

        <div class="bookings-filters">
            <select wire:model.live="modelFilter" class="form-control">
                <option value="all">همه مدل‌ها</option>
                <option value="professional">حرفه‌ای</option>
                <option value="free">رایگان</option>
            </select>
            <select wire:model.live="serviceFilter" class="form-control">
                <option value="all">نوع</option>
                <option value="residence">اقامتگاه</option>
                <option value="tour">تور</option>
                <option value="foodstore">رستوران</option>
            </select>
            <input type="hidden" id="bookings_date_from" wire:model.live="dateFrom">
            <input
                type="text"
                class="form-control"
                value="{{ \App\Support\Admin\PersianDate::formatForDisplay($dateFrom) ?: $dateFrom }}"
                placeholder="از تاریخ"
                data-jalali-input
                data-target-input="bookings_date_from"
                data-date-type="date"
                autocomplete="off"
            >
            <input type="hidden" id="bookings_date_to" wire:model.live="dateTo">
            <input
                type="text"
                class="form-control"
                value="{{ \App\Support\Admin\PersianDate::formatForDisplay($dateTo) ?: $dateTo }}"
                placeholder="تا تاریخ"
                data-jalali-input
                data-target-input="bookings_date_to"
                data-date-type="date"
                autocomplete="off"
            >
            <button type="button" class="btn btn-primary" wire:click="$refresh">فیلتر</button>
        </div>

        @if($bookings->count() > 0)
            <table class="data-table" style="margin: 0; border-right: none; border-left: none; border-bottom: none;">
                <thead>
                <tr>
                    <th>کد</th>
                    <th>کاربر</th>
                    <th>خدمت</th>
                    <th>نوع</th>
                    <th>مدل</th>
                    <th>تاریخ</th>
                    <th>مبلغ</th>
                    <th>وضعیت</th>
                    <th>کارمند</th>
                </tr>
                </thead>
                <tbody>
                @foreach($bookings as $booking)
                    <tr>
                        <td>{{ $booking['code'] }}</td>
                        <td>{{ $booking['user_name'] }}</td>
                        <td>{{ $booking['service_name'] }}</td>
                        <td>{{ $booking['type_label'] }}</td>
                        <td>{{ $booking['model_label'] }}</td>
                        <td>{{ $booking['date'] }}</td>
                        <td>{{ $booking['amount'] > 0 ? number_format($booking['amount']) : '-' }}</td>
                        <td>
                            <span class="finance-badge {{ $booking['status_meta']['class'] }}">
                                {{ $booking['status_meta']['label'] }}
                            </span>
                        </td>
                        <td>{{ $booking['employee'] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
            <div class="admin-empty-state bookings-empty-state">
                <h4>رزروی یافت نشد</h4>
                <p>فیلترها را تغییر دهید یا از صفحه درخواست‌ها یک رزرو را تکمیل کنید.</p>
            </div>
        @endif
    </div>
</div>
