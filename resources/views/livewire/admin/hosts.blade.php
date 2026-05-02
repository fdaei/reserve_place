@push('head')
    <style>
        .hosts-page {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .hosts-table-card {
            border-radius: 18px;
            border: 1px solid var(--admin-border);
            background: #fff;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
            overflow: hidden;
        }

        .hosts-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 18px 18px 8px;
        }

        .hosts-head h3 {
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 1rem;
            font-weight: 700;
            color: var(--admin-text);
        }

        .hosts-head h3 i {
            color: var(--admin-primary);
        }

        .hosts-filters {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto auto;
            gap: 8px;
            padding: 0 18px 14px;
        }

        .hosts-actions {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .hosts-actions .btn:hover,
        .hosts-actions .btn:focus {
            transform: none;
            box-shadow: none;
        }

        .hosts-empty-state {
            padding: 14px 18px 20px;
        }

        @media (max-width: 900px) {
            .hosts-head,
            .hosts-filters {
                padding-right: 14px;
                padding-left: 14px;
            }

            .hosts-filters {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

<div class="section hosts-page">
    <div class="admin-section-head">
        <div>
            <h2>
                <i class="fa fa-star"></i>
                مدیریت میزبان‌ها
            </h2>
        </div>
    </div>

    <div class="hosts-table-card">
        <div class="hosts-head">
            <h3>
                <i class="fa fa-star"></i>
                مدیریت میزبان‌ها
            </h3>
        </div>

        <div class="hosts-filters">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                class="form-control"
                placeholder="جستجوی میزبان"
            >
            <button type="button" class="btn btn-primary" wire:click="$refresh">جستجو</button>
            <button type="button" class="btn btn-success" wire:click="openWalletModal">شارژ کیف پول</button>
        </div>

        @if($list->count() > 0)
            <table class="data-table" style="margin: 0; border-right: none; border-left: none; border-bottom: none;">
                <thead>
                <tr>
                    <th>نام</th>
                    <th>تعداد اقامتگاه</th>
                    <th>تعداد تور</th>
                    <th>تعداد رستوران</th>
                    <th>موجودی کیف</th>
                    <th>درآمد کل</th>
                    <th>در انتظار تسویه</th>
                    <th>عملیات</th>
                </tr>
                </thead>
                <tbody>
                @foreach($list as $item)
                    <tr>
                        <td>{{ $item->row_full_name }}</td>
                        <td>{{ $item->residences_count }}</td>
                        <td>{{ $item->tours_count }}</td>
                        <td>{{ $item->foodstores_count }}</td>
                        <td>{{ number_format($item->row_wallet_balance) }}</td>
                        <td>{{ number_format($item->row_total_income) }}</td>
                        <td>{{ number_format($item->row_pending_settlement) }}</td>
                        <td>
                            <div class="hosts-actions">
                                <button type="button" class="btn btn-sm btn-primary" wire:click="openWalletModal('{{ $item->id }}')">شارژ</button>
                                <button type="button" class="btn btn-sm btn-secondary" wire:click="openSettlementModal('{{ $item->id }}')">تسویه</button>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="card" style="border: none; border-top: 1px solid var(--admin-border); border-radius: 0;">
                <div class="card-body">
                    {{ $list->links('vendor.pagination.default') }}
                </div>
            </div>
        @else
            <div class="admin-empty-state hosts-empty-state">
                <h4>میزبانی یافت نشد</h4>
                <p>جستجو را تغییر دهید یا ابتدا محتوایی برای کاربران ثبت شود.</p>
            </div>
        @endif
    </div>

    <div class="modal fade {{ $walletForm != 'empty' ? 'show' : '' }}" tabindex="-1"
         aria-hidden="true"
         style="{{ $walletForm != 'empty' ? 'display: block;' : '' }}">
        <div class="modal-dialog">
            <form wire:submit="saveWalletAction" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ $walletMode === 'settlement' ? 'تسویه حساب میزبان' : 'شارژ کیف پول میزبان' }}
                    </h5>
                    <span wire:click="$set('walletForm','empty')" type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </span>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>میزبان</label>
                        <select wire:model="selectedHostId" class="form-control">
                            <option value="">انتخاب میزبان</option>
                            @foreach($hostOptions as $host)
                                <option value="{{ $host->id }}">{{ trim(($host->name ?? '') . ' ' . ($host->family ?? '')) ?: ('کاربر #' . $host->id) }}</option>
                            @endforeach
                        </select>
                        @error('selectedHostId')
                            <div class="text-danger text-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>مبلغ</label>
                        <input type="number" wire:model="walletAmount" min="1" class="form-control">
                        @error('walletAmount')
                            <div class="text-danger text-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <span wire:click="$set('walletForm','empty')" type="button" class="btn btn-secondary" data-dismiss="modal">لغو</span>
                    <button class="btn btn-primary">ذخیره</button>
                </div>
            </form>
        </div>
    </div>

    @script
    <script>
        Livewire.on("saved", event => {
            Toast.fire({
                icon: 'success',
                title: 'عملیات کیف پول با موفقیت ثبت شد'
            })
        })

        Livewire.on("info", event => {
            Toast.fire({
                icon: 'info',
                title: 'مبلغی برای تسویه وجود ندارد'
            })
        })
    </script>
    @endscript
</div>
