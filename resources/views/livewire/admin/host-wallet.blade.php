@push('head')
    <style>
        .host-wallet-page {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .wallet-card {
            border-radius: 18px;
            border: 1px solid var(--admin-border);
            background: #fff;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
            overflow: hidden;
        }

        .wallet-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 18px 18px 8px;
        }

        .wallet-head h3 {
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 1rem;
            font-weight: 700;
            color: var(--admin-text);
        }

        .wallet-head h3 i {
            color: var(--admin-primary);
        }

        .wallet-summary {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
            padding: 0 18px 16px;
        }

        .wallet-summary-card {
            min-height: 92px;
            padding: 18px;
            border-radius: 14px;
            background: #f8fafc;
            border: 1px solid #edf2f7;
        }

        .wallet-summary-card span {
            display: block;
            margin-bottom: 10px;
            color: #64748b;
            font-size: 0.78rem;
        }

        .wallet-summary-card strong {
            display: block;
            color: #111827;
            font-size: 1.25rem;
            font-weight: 800;
        }

        .wallet-form {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) minmax(0, 2fr) auto;
            gap: 8px;
            align-items: start;
            padding: 0 18px 20px;
        }

        .wallet-field {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .wallet-table-title {
            margin: 0;
            padding: 0 18px 12px;
            font-size: 0.95rem;
            font-weight: 800;
            color: #111827;
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

        .finance-badge.danger {
            color: #b91c1c;
            background: #fee2e2;
        }

        .wallet-amount-positive {
            color: #10b981;
            font-weight: 800;
        }

        .wallet-amount-negative {
            color: #ef4444;
            font-weight: 800;
        }

        .wallet-empty-state {
            padding: 14px 18px 20px;
        }

        @media (max-width: 900px) {
            .wallet-head,
            .wallet-summary,
            .wallet-form {
                padding-right: 14px;
                padding-left: 14px;
            }

            .wallet-summary,
            .wallet-form {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

<div class="section host-wallet-page">
    <div class="admin-section-head">
        <div>
            <h2>
                <i class="fa fa-credit-card"></i>
                کیف پول میزبان‌ها
            </h2>
        </div>
    </div>

    <div class="wallet-card">
        <div class="wallet-head">
            <h3>
                <i class="fa fa-credit-card"></i>
                کیف پول میزبان‌ها
            </h3>
        </div>

        <div class="wallet-summary">
            <div class="wallet-summary-card">
                <span>موجودی کل کیف پول میزبان‌ها</span>
                <strong>{{ number_format($totalBalance) }} تومان</strong>
            </div>
            <div class="wallet-summary-card">
                <span>درخواست‌های پرداخت در انتظار</span>
                <strong>{{ number_format($pendingWithdrawals) }} تومان</strong>
            </div>
        </div>

        <form class="wallet-form" wire:submit="increaseBalance">
            <div class="wallet-field">
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

            <div class="wallet-field">
                <input type="text" wire:model="amount" class="form-control" placeholder="مبلغ">
                @error('amount')
                    <div class="text-danger text-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="wallet-field">
                <input type="text" wire:model="description" class="form-control" placeholder="توضیحات">
                @error('description')
                    <div class="text-danger text-error">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-success">افزایش موجودی</button>
        </form>

        <div class="wallet-form" style="padding-top: 0;">
            <select wire:model.live="hostFilter" class="form-control">
                <option value="all">همه میزبان‌ها</option>
                @foreach($hosts as $host)
                    @php
                        $hostName = trim(($host->name ?? '') . ' ' . ($host->family ?? ''));
                    @endphp
                    <option value="{{ $host->id }}">{{ $hostName !== '' ? $hostName : ('کاربر #' . $host->id) }}</option>
                @endforeach
            </select>
        </div>

        <h3 class="wallet-table-title">تاریخچه تراکنش‌ها</h3>

        @if($transactions->count() > 0)
            <table class="data-table" style="margin: 0; border-right: none; border-left: none; border-bottom: none;">
                <thead>
                <tr>
                    <th>تاریخ</th>
                    <th>میزبان</th>
                    <th>نوع</th>
                    <th>مبلغ</th>
                    <th>موجودی بعدی</th>
                    <th>توضیحات</th>
                    <th>عملیات</th>
                </tr>
                </thead>
                <tbody>
                @foreach($transactions as $transaction)
                    <tr>
                        <td>{{ $transaction['date'] }}</td>
                        <td>{{ $transaction['host_name'] }}</td>
                        <td>
                            <span class="finance-badge {{ $transaction['type_class'] }}">
                                {{ $transaction['type_label'] }}
                            </span>
                        </td>
                        <td>
                            <span class="{{ $transaction['type'] === 'decrease' ? 'wallet-amount-negative' : 'wallet-amount-positive' }}">
                                {{ $transaction['type'] === 'decrease' ? '-' : '+' }}{{ number_format($transaction['amount']) }}
                            </span>
                        </td>
                        <td>{{ number_format($transaction['balance_after']) }}</td>
                        <td>{{ $transaction['description'] }}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-light wallet-print-btn" aria-label="چاپ">
                                <i class="fa fa-print"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
            <div class="admin-empty-state wallet-empty-state">
                <h4>تراکنشی ثبت نشده</h4>
                <p>با افزایش موجودی، تاریخچه کیف پول اینجا نمایش داده می‌شود.</p>
            </div>
        @endif
    </div>

    @script
    <script>
        $(document).on("click", ".wallet-print-btn", function () {
            window.print();
        });

        Livewire.on("saved", event => {
            Toast.fire({
                icon: 'success',
                title: 'موجودی کیف پول بروزرسانی شد'
            })
        })
    </script>
    @endscript
</div>
