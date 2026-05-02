@push('head')
    <style>
        .withdraw-page {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .withdraw-card {
            border-radius: 18px;
            border: 1px solid var(--admin-border);
            background: #fff;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
            overflow: hidden;
        }

        .withdraw-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 18px 18px 8px;
        }

        .withdraw-head h3 {
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 1rem;
            font-weight: 800;
            color: var(--admin-text);
        }

        .withdraw-head h3 i {
            color: var(--admin-primary);
        }

        .withdraw-summary {
            min-height: 86px;
            margin: 0 18px 18px;
            padding: 16px 18px;
            border-radius: 14px;
            border: 1px solid #edf2f7;
            background: #f8fafc;
        }

        .withdraw-summary span {
            display: block;
            margin-bottom: 8px;
            color: #64748b;
            font-size: 0.78rem;
        }

        .withdraw-summary strong {
            display: block;
            color: #111827;
            font-size: 1.35rem;
            font-weight: 800;
        }

        .withdraw-summary small {
            display: block;
            margin-top: 8px;
            color: #64748b;
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

        .withdraw-actions {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .withdraw-settlement {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) minmax(0, 1fr) auto;
            gap: 8px;
            align-items: start;
            padding: 18px;
            border-top: 1px solid var(--admin-border);
        }

        .withdraw-settlement-title {
            margin: 0;
            padding: 16px 18px 0;
            font-size: 0.95rem;
            font-weight: 800;
            color: #111827;
            border-top: 1px solid var(--admin-border);
        }

        .withdraw-field {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .withdraw-empty-state {
            padding: 14px 18px 20px;
        }

        @media (max-width: 900px) {
            .withdraw-head,
            .withdraw-settlement {
                padding-right: 14px;
                padding-left: 14px;
            }

            .withdraw-summary {
                margin-right: 14px;
                margin-left: 14px;
            }

            .withdraw-settlement {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

<div class="section withdraw-page">
    <div class="admin-section-head">
        <div>
            <h2>
                <i class="fa fa-money"></i>
                درخواست‌های برداشت
            </h2>
        </div>
    </div>

    <div class="withdraw-card">
        <div class="withdraw-head">
            <h3>
                <i class="fa fa-money"></i>
                درخواست‌های برداشت
            </h3>
        </div>

        <div class="withdraw-summary">
            <span>کل درخواست‌ها</span>
            <strong>{{ number_format($totalRequested) }}</strong>
            <small>تعداد درخواست: {{ $requestCount }}</small>
        </div>

        @if($requests->count() > 0)
            <table class="data-table" style="margin: 0; border-right: none; border-left: none; border-bottom: none;">
                <thead>
                <tr>
                    <th>میزبان</th>
                    <th>موجودی کیف</th>
                    <th>مبلغ درخواستی</th>
                    <th>شماره کارت</th>
                    <th>تاریخ درخواست</th>
                    <th>وضعیت</th>
                    <th>عملیات</th>
                </tr>
                </thead>
                <tbody>
                @foreach($requests as $request)
                    <tr>
                        <td>{{ $request['host_name'] }}</td>
                        <td>{{ number_format($request['wallet_balance']) }}</td>
                        <td>{{ number_format($request['amount']) }}</td>
                        <td>{{ $request['card_number'] }}</td>
                        <td>{{ $request['date'] }}</td>
                        <td>
                            <span class="finance-badge {{ $request['status_meta']['class'] }}">
                                {{ $request['status_meta']['label'] }}
                            </span>
                        </td>
                        <td>
                            <div class="withdraw-actions">
                                @if($request['status'] === 'pending')
                                    <button type="button" class="btn btn-sm btn-success" wire:click="approve('{{ $request['id'] }}')">تایید واریز</button>
                                    <button type="button" class="btn btn-sm btn-warning" wire:click="reject('{{ $request['id'] }}')">رد</button>
                                @else
                                    <button type="button" class="btn btn-sm btn-secondary" disabled>مشاهده</button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
            <div class="admin-empty-state withdraw-empty-state">
                <h4>درخواستی ثبت نشده</h4>
                <p>بعد از ثبت تسویه یا درخواست برداشت، اینجا نمایش داده می‌شود.</p>
            </div>
        @endif

        <h3 class="withdraw-settlement-title">ثبت تسویه دستی</h3>
        <form class="withdraw-settlement" wire:submit="registerManualSettlement">
            <div class="withdraw-field">
                <select wire:model="selectedHostId" class="form-control">
                    <option value="">انتخاب میزبان</option>
                    @foreach($hosts as $host)
                        @php
                            $hostName = trim(($host->name ?? '') . ' ' . ($host->family ?? ''));
                        @endphp
                        <option value="{{ $host->id }}">{{ $hostName !== '' ? $hostName : ('کاربر #' . $host->id) }}</option>
                    @endforeach
                </select>
                @error('selectedHostId')
                    <div class="text-danger text-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="withdraw-field">
                <input type="text" wire:model="amount" class="form-control" placeholder="مبلغ">
                @error('amount')
                    <div class="text-danger text-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="withdraw-field">
                <input type="text" wire:model="cardNumber" class="form-control" placeholder="شماره کارت">
                @error('cardNumber')
                    <div class="text-danger text-error">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-success">ثبت تسویه</button>
        </form>
    </div>

    @script
    <script>
        Livewire.on("saved", event => {
            Toast.fire({
                icon: 'success',
                title: 'وضعیت برداشت بروزرسانی شد'
            })
        })
    </script>
    @endscript
</div>
