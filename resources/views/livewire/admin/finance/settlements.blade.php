@push('head')
    <style>
        .settlement-stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 16px;
        }

        .settlement-stat {
            min-height: 88px;
            padding: 16px;
            border-radius: 8px;
            border: 1px solid var(--admin-border);
            background: #fff;
        }

        .settlement-stat span {
            display: block;
            margin-bottom: 8px;
            color: var(--admin-muted);
            font-size: .8rem;
        }

        .settlement-stat strong {
            font-size: 1.15rem;
            font-weight: 800;
        }

        @media (max-width: 1100px) {
            .settlement-stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .settlement-stats {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

<div class="section listing-panel">
    <div class="listing-panel-head">
        <div>
            <h2 class="listing-panel-title">
                <span class="listing-panel-icon"><i class="fa fa-check-square-o"></i></span>
                تسویه حساب‌ها
            </h2>
            <p class="admin-page-description">فیلتر میزبان، بازه تاریخ و جستجو روی شماره کارت، شبا و نام میزبان.</p>
        </div>

        <div class="pages-head-actions">
            <a href="{{ route('admin.resources.create', 'settlements') }}" class="toolbar-btn toolbar-btn--success">
                <span>+</span>
                تسویه جدید
            </a>
        </div>
    </div>

    <div class="settlement-stats">
        <div class="settlement-stat">
            <span>مبلغ کل تسویه‌ها</span>
            <strong>{{ number_format($stats['total']) }} تومان</strong>
        </div>
        <div class="settlement-stat">
            <span>در انتظار بررسی</span>
            <strong>{{ number_format($stats['pending']) }} تومان</strong>
        </div>
        <div class="settlement-stat">
            <span>واریز شده</span>
            <strong>{{ number_format($stats['paid']) }} تومان</strong>
        </div>
        <div class="settlement-stat">
            <span>تعداد رکورد</span>
            <strong>{{ number_format($stats['count']) }}</strong>
        </div>
    </div>

    <div class="listing-toolbar">
        <div class="listing-toolbar-main">
            <input type="text" wire:model.live.debounce.300ms="search" class="listing-search" placeholder="جستجو">
            <select wire:model.live="hostFilter">
                <option value="all">همه میزبان‌ها</option>
                @foreach($hosts as $host)
                    <option value="{{ $host->id }}">{{ $host->full_name }} - {{ $host->phone }}</option>
                @endforeach
            </select>
            <select wire:model.live="statusFilter">
                <option value="all">همه وضعیت‌ها</option>
                @foreach($statusOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
            <input type="hidden" id="settlement_from_date" wire:model.live="fromDate">
            <input
                type="text"
                class="listing-search"
                value="{{ \App\Support\Admin\PersianDate::formatForDisplay($fromDate) ?: $fromDate }}"
                placeholder="از تاریخ"
                data-jalali-input
                data-target-input="settlement_from_date"
                data-date-type="date"
                autocomplete="off"
            >
            <input type="hidden" id="settlement_to_date" wire:model.live="toDate">
            <input
                type="text"
                class="listing-search"
                value="{{ \App\Support\Admin\PersianDate::formatForDisplay($toDate) ?: $toDate }}"
                placeholder="تا تاریخ"
                data-jalali-input
                data-target-input="settlement_to_date"
                data-date-type="date"
                autocomplete="off"
            >
        </div>
        <div class="listing-toolbar-actions">
            <button type="button" class="toolbar-btn toolbar-btn--dark" wire:click="$refresh">فیلتر</button>
        </div>
    </div>

    @if($settlements->count())
        <div class="listing-table-wrap">
            <table class="table responsive-table listing-table">
                <thead>
                <tr>
                    <th>میزبان</th>
                    <th>مبلغ تسویه</th>
                    <th>شماره کارت</th>
                    <th>شبا</th>
                    <th>تاریخ درخواست</th>
                    <th>تاریخ واریز</th>
                    <th>وضعیت</th>
                    <th>عملیات</th>
                </tr>
                </thead>
                <tbody>
                @foreach($settlements as $settlement)
                    <tr>
                        <td data-label="میزبان">{{ $settlement->host?->full_name ?: '-' }}</td>
                        <td data-label="مبلغ تسویه">{{ number_format((int) $settlement->amount) }} تومان</td>
                        <td data-label="شماره کارت">{{ $settlement->card_number ?: '-' }}</td>
                        <td data-label="شبا">{{ $settlement->iban ?: '-' }}</td>
                        <td data-label="تاریخ درخواست">{{ \App\Support\Admin\AdminResourceRegistry::displayValue($settlement, ['key' => 'requested_at', 'type' => 'datetime']) }}</td>
                        <td data-label="تاریخ واریز">{{ \App\Support\Admin\AdminResourceRegistry::displayValue($settlement, ['key' => 'paid_at', 'type' => 'datetime']) }}</td>
                        <td data-label="وضعیت"><x-admin.status-badge :value="$settlement->status" /></td>
                        <td data-label="عملیات">
                            <div class="listing-actions">
                                <a href="{{ route('admin.resources.edit', ['settlements', $settlement->id]) }}" class="listing-icon-btn" title="ویرایش" aria-label="ویرایش">
                                    <i class="fa fa-pencil-square-o"></i>
                                </a>
                                <a href="{{ route('admin.resources.show', ['settlements', $settlement->id]) }}" class="listing-icon-btn" title="نمایش" aria-label="نمایش">
                                    <i class="fa fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="listing-pagination">
            <div class="card">
                <div class="card-body">{{ $settlements->links('vendor.pagination.bootstrap-4') }}</div>
            </div>
        </div>
    @else
        <x-admin.empty-state title="تسویه‌ای یافت نشد" description="فیلترها را تغییر دهید یا تسویه جدید ثبت کنید." />
    @endif
</div>
