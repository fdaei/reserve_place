@push('head')
    <style>
        .discounts-page {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .discounts-card {
            border-radius: 18px;
            border: 1px solid var(--admin-border);
            background: #fff;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
            overflow: hidden;
        }

        .discounts-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 18px 18px 8px;
        }

        .discounts-head h3 {
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 1rem;
            font-weight: 800;
            color: var(--admin-text);
        }

        .discounts-head h3 i {
            color: var(--admin-primary);
        }

        .discount-form {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) minmax(0, 1fr);
            gap: 8px;
            padding: 0 18px 10px;
        }

        .discount-form-bottom {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto auto;
            gap: 8px;
            padding: 0 18px 18px;
        }

        .discount-field {
            display: flex;
            flex-direction: column;
            gap: 6px;
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

        .discount-actions {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        @media (max-width: 900px) {
            .discounts-head,
            .discount-form,
            .discount-form-bottom {
                padding-right: 14px;
                padding-left: 14px;
            }

            .discount-form,
            .discount-form-bottom {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

<div class="section discounts-page">
    <div class="admin-section-head">
        <div>
            <h2>
                <i class="fa fa-ticket"></i>
                کد تخفیف
            </h2>
        </div>
    </div>

    <div class="discounts-card">
        <div class="discounts-head">
            <h3>
                <i class="fa fa-ticket"></i>
                کد تخفیف
            </h3>
        </div>

        <form wire:submit="saveDiscount">
            <div class="discount-form">
                <div class="discount-field">
                    <input type="text" wire:model="code" class="form-control" placeholder="کد تخفیف">
                    @error('code')
                        <div class="text-danger text-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="discount-field">
                    <input type="text" wire:model="value" class="form-control" placeholder="درصد یا مبلغ">
                    @error('value')
                        <div class="text-danger text-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="discount-field">
                    <input type="text" wire:model="expiresAt" class="form-control" placeholder="تاریخ انقضا">
                    @error('expiresAt')
                        <div class="text-danger text-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="discount-form-bottom">
                <select wire:model="scope" class="form-control">
                    <option value="all">همه موارد</option>
                    <option value="residence">اقامتگاه</option>
                    <option value="tour">تور</option>
                    <option value="foodstore">رستوران</option>
                </select>
                <button type="submit" class="btn btn-primary">{{ $editingId ? 'ذخیره تغییرات' : 'ثبت کد تخفیف' }}</button>
                @if($editingId)
                    <button type="button" class="btn btn-secondary" wire:click="resetForm">لغو</button>
                @endif
            </div>
        </form>

        <table class="data-table" style="margin: 0; border-right: none; border-left: none; border-bottom: none;">
            <thead>
            <tr>
                <th>کد</th>
                <th>مقدار</th>
                <th>بخش</th>
                <th>تاریخ انقضا</th>
                <th>تعداد استفاده</th>
                <th>وضعیت</th>
                <th>عملیات</th>
            </tr>
            </thead>
            <tbody>
            @foreach($discounts as $discount)
                <tr>
                    <td>{{ $discount['code'] }}</td>
                    <td>{{ $discount['value'] }}</td>
                    <td>{{ $discount['scope_label'] }}</td>
                    <td>{{ $discount['expires_at'] }}</td>
                    <td>{{ $discount['usage_count'] }}</td>
                    <td>
                        <span class="finance-badge {{ $discount['status_meta']['class'] }}">
                            {{ $discount['status_meta']['label'] }}
                        </span>
                    </td>
                    <td>
                        <div class="discount-actions">
                            <button type="button" class="btn btn-sm btn-warning" wire:click="editDiscount('{{ $discount['id'] }}')" aria-label="ویرایش">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-secondary" wire:click="toggleStatus('{{ $discount['id'] }}')">
                                {{ $discount['status'] === 'active' ? 'غیرفعال' : 'فعال' }}
                            </button>
                            <button type="button" class="btn btn-sm btn-danger discounts-remove-btn" data-id="{{ $discount['id'] }}" data-title="{{ $discount['code'] }}" aria-label="حذف">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    @script
    <script>
        $(document).on("click", ".discounts-remove-btn", function () {
            let id = $(this).attr("data-id")
            let title = $(this).attr("data-title")
            Swal.fire({
                icon: "warning",
                title: 'هشدار',
                text: `از حذف کردن ${title} اطمینان دارید؟`,
                confirmButtonText: "لغو",
                denyButtonText: "حذف کردن",
                showDenyButton: true,
                background: '#333',
                color: '#fff',
                confirmButtonColor: '#3085d6',
            }).then(res => {
                if (res.isDenied) {
                    Livewire.dispatch("remove", { id: id });
                }
            })
        });

        Livewire.on("saved", event => {
            Toast.fire({
                icon: 'success',
                title: 'کد تخفیف ذخیره شد'
            })
        })

        Livewire.on("removed", event => {
            Toast.fire({
                icon: 'success',
                title: 'کد تخفیف حذف شد'
            })
        })
    </script>
    @endscript
</div>
