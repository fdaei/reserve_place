@push('head')
    <style>
        .users-page {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .users-table-card {
            border-radius: 18px;
            border: 1px solid var(--admin-border);
            background: #fff;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
            overflow: hidden;
        }

        .users-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 18px 18px 8px;
        }

        .users-head h3 {
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 1rem;
            font-weight: 700;
            color: var(--admin-text);
        }

        .users-head h3 i {
            color: var(--admin-primary);
        }

        .users-filters {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 170px auto;
            gap: 8px;
            padding: 0 18px 14px;
        }

        .users-filters .btn {
            min-width: 66px;
        }

        .users-actions {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .users-actions .btn:hover,
        .users-actions .btn:focus {
            transform: none;
            box-shadow: none;
        }

        .users-empty-state {
            padding: 14px 18px 20px;
        }

        @media (max-width: 900px) {
            .users-head,
            .users-filters {
                padding-right: 14px;
                padding-left: 14px;
            }

            .users-filters {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

<div class="section users-page">
    <div class="admin-section-head">
        <div>
            <h2>
                <i class="fa fa-users"></i>
                مدیریت کاربران عادی
            </h2>
        </div>
    </div>

    <div class="users-table-card">
        <div class="users-head">
            <h3>
                <i class="fa fa-users"></i>
                مدیریت کاربران عادی
            </h3>
        </div>

        <div class="users-filters">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                class="form-control"
                placeholder="جستجوی نام یا موبایل"
            >
            <select wire:model.live="statusFilter" class="form-control">
                <option value="all">همه وضعیت‌ها</option>
                <option value="active">فعال</option>
                <option value="pending">در انتظار</option>
            </select>
            <button type="button" class="btn btn-primary" wire:click="$refresh">جستجو</button>
        </div>

        @if($list->count() > 0)
            <table class="data-table" style="margin: 0; border-right: none; border-left: none; border-bottom: none;">
                <thead>
                <tr>
                    <th>نام</th>
                    <th>موبایل</th>
                    <th>تاریخ ثبت</th>
                    <th>مدل‌های استفاده شده</th>
                    <th>وضعیت</th>
                    <th>عملیات</th>
                </tr>
                </thead>
                <tbody>
                @foreach($list as $item)
                    @php
                        $gregorianDate = new \DateTime($item['created_at']);
                        $jalaliDate = \Morilog\Jalali\Jalalian::fromDateTime($gregorianDate);
                    @endphp
                    <tr>
                        <td>{{ $item->row_full_name }}</td>
                        <td>{{ $item->phone }}</td>
                        <td>{{ $jalaliDate->format('%Y/%m/%d') }}</td>
                        <td>{{ $item->row_model_usage }}</td>
                        <td>
                            <span class="status-chip {{ $item->row_status['class'] }}">
                                {{ $item->row_status['label'] }}
                            </span>
                        </td>
                        <td>
                            <div class="users-actions">
                                <button type="button" class="btn btn-sm btn-warning" wire:click="login('{{ $item->id }}')">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-danger users-remove-btn"
                                    data-id="{{ $item->id }}"
                                    data-title="{{ $item->row_full_name }}"
                                >
                                    <i class="fa fa-ban"></i>
                                </button>
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
            <div class="admin-empty-state users-empty-state">
                <h4>کاربری یافت نشد</h4>
                <p>جستجو یا فیلتر را تغییر دهید.</p>
            </div>
        @endif
    </div>

    @script
    <script>
        $(document).on("click", ".users-remove-btn", function () {
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

        Livewire.on("removed", event => {
            Toast.fire({
                icon: 'success',
                title: 'سطر با موفقیت حذف شد'
            })
        })
    </script>
    @endscript
</div>
