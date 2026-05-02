@push('head')
    <style>
        .booking-request-grid {
            display: grid;
            gap: 12px;
        }

        .booking-request-card {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 14px;
            align-items: center;
            padding: 16px;
            border: 1px solid var(--admin-border);
            border-radius: 8px;
            background: #fff;
        }

        .booking-request-card h3 {
            margin: 0 0 10px;
            font-size: 1rem;
            font-weight: 800;
            color: var(--admin-text);
        }

        .booking-request-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px 14px;
            color: var(--admin-muted);
            font-size: .82rem;
        }

        .booking-request-actions {
            display: inline-flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            gap: 8px;
        }

        @media (max-width: 900px) {
            .booking-request-card {
                grid-template-columns: 1fr;
            }

            .booking-request-actions {
                justify-content: flex-start;
            }
        }
    </style>
@endpush

<div class="section listing-panel">
    <div class="listing-panel-head">
        <div>
            <h2 class="listing-panel-title">
                <span class="listing-panel-icon"><i class="fa fa-phone"></i></span>
                درخواست‌های رزرو
            </h2>
            <p class="admin-page-description">نمای کارتی درخواست‌ها با میزبان، بازه تاریخ، مبلغ و عملیات سریع.</p>
        </div>

        <div class="pages-head-actions">
            <a href="{{ route('admin.resources.create', 'booking-requests') }}" class="toolbar-btn toolbar-btn--success">
                <span>+</span>
                درخواست جدید
            </a>
        </div>
    </div>

    <div class="listing-toolbar">
        <div class="listing-toolbar-main">
            <input type="text" wire:model.live.debounce.300ms="search" class="listing-search" placeholder="جستجوی مهمان، میزبان یا اقامتگاه">
            <select wire:model.live="statusFilter">
                <option value="all">همه وضعیت‌ها</option>
                @foreach($statusOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
            <select wire:model.live="hostFilter">
                <option value="all">همه میزبان‌ها</option>
                @foreach($hosts as $host)
                    <option value="{{ $host->id }}">{{ $host->full_name }} - {{ $host->phone }}</option>
                @endforeach
            </select>
            <input type="hidden" id="booking_request_from" wire:model.live="fromDate">
            <input
                type="text"
                class="listing-search"
                value="{{ \App\Support\Admin\PersianDate::formatForDisplay($fromDate) ?: $fromDate }}"
                placeholder="از تاریخ"
                data-jalali-input
                data-target-input="booking_request_from"
                data-date-type="date"
                autocomplete="off"
            >
            <input type="hidden" id="booking_request_to" wire:model.live="toDate">
            <input
                type="text"
                class="listing-search"
                value="{{ \App\Support\Admin\PersianDate::formatForDisplay($toDate) ?: $toDate }}"
                placeholder="تا تاریخ"
                data-jalali-input
                data-target-input="booking_request_to"
                data-date-type="date"
                autocomplete="off"
            >
        </div>
        <div class="listing-toolbar-actions">
            <button type="button" class="toolbar-btn toolbar-btn--dark" wire:click="$refresh">فیلتر</button>
        </div>
    </div>

    @if($requests->count())
        <div class="booking-request-grid">
            @foreach($requests as $request)
                <article class="booking-request-card">
                    <div>
                        <h3>{{ data_get($request, 'bookable.title') ?: data_get($request, 'bookable.name') ?: 'اقامتگاه حذف شده' }}</h3>
                        <div class="booking-request-meta">
                            <span>میزبان: {{ $request->host?->full_name ?: '-' }}</span>
                            <span>مهمان: {{ $request->customer?->full_name ?: $request->guest_name }}</span>
                            <span>
                                {{ \App\Support\Admin\AdminResourceRegistry::displayValue($request, ['key' => 'starts_at', 'type' => 'date']) }}
                                تا
                                {{ \App\Support\Admin\AdminResourceRegistry::displayValue($request, ['key' => 'ends_at', 'type' => 'date']) }}
                            </span>
                            <span>{{ number_format((int) $request->total_amount) }} تومان</span>
                            <span><x-admin.status-badge :value="$request->status" /></span>
                        </div>
                    </div>

                    <div class="booking-request-actions">
                        @if($request->status === 'pending')
                            <button type="button" class="listing-action-btn listing-action-btn--success" wire:click="approveRequest({{ $request->id }})">تأیید میزبان</button>
                            <button type="button" class="listing-action-btn listing-action-btn--danger" wire:click="rejectRequest({{ $request->id }})">رد</button>
                        @elseif($request->status === 'approved' && $request->payment_status !== 'paid')
                            <button type="button" class="listing-action-btn listing-action-btn--info" wire:click="markPaid({{ $request->id }})">ثبت پرداخت</button>
                        @elseif($request->payment_status === 'paid' && $request->settlement_status === 'blocked')
                            <button type="button" class="listing-action-btn listing-action-btn--success" wire:click="releaseAmount({{ $request->id }})">آزادسازی مبلغ</button>
                        @elseif($request->settlement_status === 'releasable')
                            <button type="button" class="listing-action-btn listing-action-btn--dark" wire:click="settleWithHost({{ $request->id }})">تسویه با میزبان</button>
                        @endif

                        <a href="{{ route('admin.resources.show', ['booking-requests', $request->id]) }}" class="listing-action-btn listing-action-btn--info">جزئیات</a>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="listing-pagination">
            <div class="card">
                <div class="card-body">{{ $requests->links('vendor.pagination.bootstrap-4') }}</div>
            </div>
        </div>
    @else
        <x-admin.empty-state title="درخواستی یافت نشد" description="فیلترها را تغییر دهید یا درخواست جدید ثبت کنید." />
    @endif

    @script
    <script>
        Livewire.on('booking-request-saved', () => {
            Toast.fire({ icon: 'success', title: 'وضعیت درخواست رزرو بروزرسانی شد' });
        });
    </script>
    @endscript
</div>
