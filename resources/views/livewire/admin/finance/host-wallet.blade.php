@push('head')
    <style>
        .wallet-summary-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 16px;
        }

        .wallet-summary-item {
            min-height: 92px;
            padding: 16px;
            border: 1px solid var(--admin-border);
            border-radius: 8px;
            background: #fff;
        }

        .wallet-summary-item span {
            display: block;
            margin-bottom: 10px;
            color: var(--admin-muted);
            font-size: .8rem;
        }

        .wallet-summary-item strong {
            color: var(--admin-text);
            font-size: 1.2rem;
            font-weight: 800;
        }

        .wallet-modal-backdrop {
            position: fixed;
            inset: 0;
            z-index: 1040;
            background: rgba(15, 23, 42, .42);
        }

        .wallet-modal {
            position: fixed;
            inset: 0;
            z-index: 1050;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 18px;
        }

        .wallet-modal-panel {
            width: min(520px, 100%);
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 24px 60px rgba(15, 23, 42, .22);
        }

        .wallet-modal-head,
        .wallet-modal-body,
        .wallet-modal-actions {
            padding: 16px 18px;
        }

        .wallet-modal-head {
            border-bottom: 1px solid var(--admin-border);
        }

        .wallet-modal-head h3 {
            margin: 0;
            font-size: 1rem;
            font-weight: 800;
        }

        .wallet-modal-body {
            display: grid;
            gap: 12px;
        }

        .wallet-modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            border-top: 1px solid var(--admin-border);
        }

        @media (max-width: 1100px) {
            .wallet-summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .wallet-summary-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

<div class="section listing-panel">
    <div class="listing-panel-head">
        <div>
            <h2 class="listing-panel-title">
                <span class="listing-panel-icon"><i class="fa fa-money"></i></span>
                کیف پول میزبان‌ها
            </h2>
            <p class="admin-page-description">مانده، مبلغ مسدود، مبلغ قابل برداشت و درخواست‌های فعال هر میزبان.</p>
        </div>

        <div class="pages-head-actions">
            <button type="button" class="toolbar-btn toolbar-btn--success" wire:click="openChargeModal">
                <span>+</span>
                شارژ کیف پول
            </button>
        </div>
    </div>

    <div class="wallet-summary-grid">
        <div class="wallet-summary-item">
            <span>موجودی کل</span>
            <strong>{{ number_format($stats['total']) }} تومان</strong>
        </div>
        <div class="wallet-summary-item">
            <span>موجودی مسدود</span>
            <strong>{{ number_format($stats['blocked']) }} تومان</strong>
        </div>
        <div class="wallet-summary-item">
            <span>قابل برداشت</span>
            <strong>{{ number_format($stats['withdrawable']) }} تومان</strong>
        </div>
        <div class="wallet-summary-item">
            <span>برداشت‌های در انتظار</span>
            <strong>{{ number_format($stats['pendingWithdrawals']) }} تومان</strong>
        </div>
    </div>

    <div class="listing-toolbar">
        <div class="listing-toolbar-main">
            <input type="text" wire:model.live.debounce.300ms="search" class="listing-search" placeholder="جستجوی نام یا موبایل میزبان">
            <select wire:model.live="hostFilter">
                <option value="all">همه میزبان‌ها</option>
                @foreach($hostOptions as $host)
                    <option value="{{ $host->id }}">{{ $host->full_name }} - {{ $host->phone }}</option>
                @endforeach
            </select>
        </div>
        <div class="listing-toolbar-actions">
            <button type="button" class="toolbar-btn toolbar-btn--dark" wire:click="$refresh">فیلتر</button>
            <button type="button" class="toolbar-btn toolbar-btn--light" wire:click="$set('search', '')" wire:loading.attr="disabled">پاک‌سازی</button>
        </div>
    </div>

    @if($hosts->count())
        <div class="listing-table-wrap">
            <table class="table responsive-table listing-table">
                <thead>
                <tr>
                    <th>میزبان</th>
                    <th>تعداد اقامتگاه</th>
                    <th>موجودی کل</th>
                    <th>مسدود</th>
                    <th>قابل برداشت</th>
                    <th>درخواست برداشت فعال</th>
                    <th>عملیات</th>
                </tr>
                </thead>
                <tbody>
                @foreach($hosts as $host)
                    <tr>
                        <td data-label="میزبان">{{ $host->row_full_name }}</td>
                        <td data-label="تعداد اقامتگاه">{{ number_format($host->residences_count) }}</td>
                        <td data-label="موجودی کل">{{ number_format($host->row_total_balance) }} تومان</td>
                        <td data-label="مسدود">{{ number_format($host->row_blocked_balance) }} تومان</td>
                        <td data-label="قابل برداشت">{{ number_format($host->row_withdrawable_balance) }} تومان</td>
                        <td data-label="درخواست برداشت فعال">
                            @if($host->row_active_withdrawal)
                                <x-admin.status-badge value="pending" />
                            @else
                                -
                            @endif
                        </td>
                        <td data-label="عملیات">
                            <div class="listing-actions">
                                <button type="button" class="listing-action-btn listing-action-btn--success" wire:click="openChargeModal({{ $host->id }})">شارژ کیف پول</button>
                                <a href="{{ route('admin.resources.index', 'wallet-transactions') }}?search={{ urlencode($host->phone) }}" class="listing-action-btn listing-action-btn--info">تراکنش‌ها</a>
                                <a href="{{ route('admin.resources.create', 'settlements') }}?host_id={{ $host->id }}" class="listing-action-btn listing-action-btn--dark">تسویه</a>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="listing-pagination">
            <div class="card">
                <div class="card-body">{{ $hosts->links('vendor.pagination.bootstrap-4') }}</div>
            </div>
        </div>
    @else
        <x-admin.empty-state title="میزبانی یافت نشد" description="با تغییر فیلترها دوباره جستجو کنید." />
    @endif

    @if($chargeModalOpen)
        <div class="wallet-modal-backdrop" wire:click="closeChargeModal"></div>
        <div class="wallet-modal" role="dialog" aria-modal="true">
            <form class="wallet-modal-panel" wire:submit="chargeWallet">
                <div class="wallet-modal-head">
                    <h3>شارژ کیف پول میزبان</h3>
                </div>
                <div class="wallet-modal-body">
                    <div class="admin-form-field">
                        <label>میزبان</label>
                        <select wire:model="chargeHostId" class="form-control">
                            <option value="">انتخاب میزبان</option>
                            @foreach($hostOptions as $host)
                                <option value="{{ $host->id }}">{{ $host->full_name }} - {{ $host->phone }}</option>
                            @endforeach
                        </select>
                        @error('chargeHostId')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                    <div class="admin-form-field">
                        <label>مبلغ</label>
                        <input type="text" wire:model.live="chargeAmount" class="form-control" inputmode="numeric">
                        @error('chargeAmount')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                    <div class="admin-form-field">
                        <label>توضیحات</label>
                        <textarea wire:model="chargeDescription" class="form-control"></textarea>
                        @error('chargeDescription')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                </div>
                <div class="wallet-modal-actions">
                    <button type="button" class="btn btn-secondary" wire:click="closeChargeModal">لغو</button>
                    <button type="submit" class="btn btn-primary">تایید شارژ</button>
                </div>
            </form>
        </div>
    @endif

    @script
    <script>
        Livewire.on('wallet-charged', () => {
            Toast.fire({ icon: 'success', title: 'کیف پول میزبان شارژ شد' });
        });
    </script>
    @endscript
</div>
