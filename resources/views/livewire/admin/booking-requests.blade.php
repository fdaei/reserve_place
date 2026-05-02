@push('head')
    <style>
        .booking-page {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .booking-panel {
            border-radius: 18px;
            border: 1px solid var(--admin-border);
            background: #fff;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
            overflow: hidden;
        }

        .booking-panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 18px 18px 8px;
        }

        .booking-panel-head h3 {
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 1rem;
            font-weight: 700;
            color: var(--admin-text);
        }

        .booking-panel-head h3 i {
            color: var(--admin-primary);
        }

        .booking-filters {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) auto;
            gap: 8px;
            padding: 0 18px 14px;
        }

        .booking-request-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding: 0 18px 18px;
        }

        .booking-request-card {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 14px;
            align-items: center;
            padding: 14px 16px;
            border-radius: 14px;
            background: #f8fafc;
            border: 1px solid #eef2f7;
        }

        .booking-request-main h4 {
            margin: 0 0 8px;
            font-size: 0.95rem;
            font-weight: 800;
            color: #111827;
        }

        .booking-request-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 6px 12px;
            color: #64748b;
            font-size: 0.78rem;
        }

        .booking-actions {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
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

        .finance-badge.pending {
            color: #9a5b00;
            background: #fff1c2;
        }

        .finance-badge.active {
            color: #047857;
            background: #d8f8e7;
        }

        .finance-badge.info {
            color: #0369a1;
            background: #dff3ff;
        }

        .finance-badge.danger {
            color: #b91c1c;
            background: #fee2e2;
        }

        .booking-empty-state {
            margin: 0 18px 18px;
        }

        @media (max-width: 900px) {
            .booking-panel-head,
            .booking-filters,
            .booking-request-list {
                padding-right: 14px;
                padding-left: 14px;
            }

            .booking-filters,
            .booking-request-card {
                grid-template-columns: 1fr;
            }

            .booking-actions {
                justify-content: flex-start;
                white-space: normal;
            }
        }
    </style>
@endpush

<div class="section booking-page">
    <div class="admin-section-head">
        <div>
            <h2>
                <i class="fa fa-phone"></i>
                درخواست‌های رزرو
            </h2>
        </div>
    </div>

    <div class="booking-panel">
        <div class="booking-panel-head">
            <h3>
                <i class="fa fa-phone"></i>
                درخواست‌های رزرو
            </h3>
        </div>

        <div class="booking-filters">
            <select wire:model.live="statusFilter" class="form-control">
                <option value="all">همه وضعیت‌ها</option>
                <option value="pending">در انتظار بررسی</option>
                <option value="assigned">اختصاص داده شده</option>
                <option value="called">تماس گرفته شد</option>
                <option value="completed">تکمیل شده</option>
                <option value="cancelled">لغو شده</option>
            </select>
            <select wire:model.live="hostFilter" class="form-control">
                <option value="all">همه کاربران</option>
                @foreach($hosts as $host)
                    @php
                        $hostName = trim(($host->name ?? '') . ' ' . ($host->family ?? ''));
                    @endphp
                    <option value="{{ $host->id }}">{{ $hostName !== '' ? $hostName : ('کاربر #' . $host->id) }}</option>
                @endforeach
            </select>
            <button type="button" class="btn btn-primary" wire:click="$refresh">فیلتر</button>
        </div>

        @if($requests->count() > 0)
            <div class="booking-request-list">
                @foreach($requests as $request)
                    <article class="booking-request-card">
                        <div class="booking-request-main">
                            <h4>درخواست رزرو {{ $request['title'] }}</h4>
                            <div class="booking-request-meta">
                                <span>نفر: {{ $request['host_name'] }}</span>
                                <span>{{ $request['date'] }}</span>
                                <span>مبلغ: {{ $request['amount'] > 0 ? number_format($request['amount']) . ' تومان' : '-' }}</span>
                                <span class="finance-badge {{ $request['status_meta']['class'] }}">{{ $request['status_meta']['label'] }}</span>
                            </div>
                        </div>

                        <div class="booking-actions">
                            @if(in_array($request['status'], ['pending', 'assigned'], true))
                                <button type="button" class="btn btn-sm btn-success" wire:click="assignToMe('{{ $request['type'] }}', {{ $request['id'] }})">اختصاص به من</button>
                                <button type="button" class="btn btn-sm btn-primary" wire:click="markCalled('{{ $request['type'] }}', {{ $request['id'] }})">تماس با مقصد</button>
                                <button type="button" class="btn btn-sm btn-warning" wire:click="cancelRequest('{{ $request['type'] }}', {{ $request['id'] }})">لغو</button>
                            @elseif($request['status'] === 'called')
                                <button type="button" class="btn btn-sm btn-success" wire:click="markCalled('{{ $request['type'] }}', {{ $request['id'] }})">تماس گرفته شد</button>
                                <button type="button" class="btn btn-sm btn-primary" wire:click="completeBooking('{{ $request['type'] }}', {{ $request['id'] }})">تکمیل رزرو</button>
                            @else
                                <span class="finance-badge {{ $request['status_meta']['class'] }}">{{ $request['status_meta']['label'] }}</span>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="admin-empty-state booking-empty-state">
                <h4>درخواستی یافت نشد</h4>
                <p>فیلترها را تغییر دهید یا ابتدا سرویس قابل رزرو ثبت شود.</p>
            </div>
        @endif
    </div>

    @script
    <script>
        Livewire.on("saved", event => {
            Toast.fire({
                icon: 'success',
                title: 'وضعیت درخواست رزرو بروزرسانی شد'
            })
        })
    </script>
    @endscript
</div>
