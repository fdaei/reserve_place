@push('head')
    <style>
        .employees-page {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .employees-table-card {
            border-radius: 18px;
            border: 1px solid var(--admin-border);
            background: #fff;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
            overflow: hidden;
        }

        .employees-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 18px 18px 8px;
        }

        .employees-head h3 {
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 1rem;
            font-weight: 700;
            color: var(--admin-text);
        }

        .employees-head h3 i {
            color: var(--admin-primary);
        }

        .employees-filters {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 190px auto auto;
            gap: 8px;
            padding: 0 18px 14px;
        }

        .employees-actions {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .employees-actions .btn:hover,
        .employees-actions .btn:focus {
            transform: none;
            box-shadow: none;
        }

        .employees-empty-state {
            padding: 14px 18px 20px;
        }

        @media (max-width: 900px) {
            .employees-head,
            .employees-filters {
                padding-right: 14px;
                padding-left: 14px;
            }

            .employees-filters {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

<div class="section employees-page">
    <div class="admin-section-head">
        <div>
            <h2>
                <i class="fa fa-user"></i>
                مدیریت کارمندان
            </h2>
        </div>
    </div>

    <div class="employees-table-card">
        <div class="employees-head">
            <h3>
                <i class="fa fa-user"></i>
                مدیریت کارمندان
            </h3>
        </div>

        <div class="employees-filters">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                class="form-control"
                placeholder="جستجوی کارمند"
            >
            <select wire:model.live="roleFilter" class="form-control">
                <option value="all">همه نقش‌ها</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                @endforeach
            </select>
            <button type="button" class="btn btn-primary" wire:click="$refresh">جستجو</button>
            <button type="button" class="btn btn-success" wire:click="setForm('add')">کارمند جدید</button>
        </div>

        @if($list->count() > 0)
            <table class="data-table" style="margin: 0; border-right: none; border-left: none; border-bottom: none;">
                <thead>
                <tr>
                    <th>نام</th>
                    <th>نقش</th>
                    <th>دسترسی</th>
                    <th>وضعیت</th>
                    <th>آخرین فعالیت</th>
                    <th>عملیات</th>
                </tr>
                </thead>
                <tbody>
                @foreach($list as $item)
                    <tr>
                        <td>{{ $item->row_full_name }}</td>
                        <td>{{ $item->row_role_name }}</td>
                        <td>{{ $item->row_permissions }}</td>
                        <td>
                            <span class="status-chip {{ $item->row_status['class'] }}">
                                {{ $item->row_status['label'] }}
                            </span>
                        </td>
                        <td>{{ $item->row_last_active }}</td>
                        <td>
                            <div class="employees-actions">
                                <button type="button" class="btn btn-sm btn-warning" wire:click="setForm('edit', '{{ $item->id }}')">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-danger employees-remove-btn"
                                    data-id="{{ $item->id }}"
                                    data-title="{{ $item->row_full_name }}"
                                >
                                    <i class="fa fa-trash"></i>
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
            <div class="admin-empty-state employees-empty-state">
                <h4>کارمندی یافت نشد</h4>
                <p>برای شروع یک کارمند جدید تعریف کنید.</p>
            </div>
        @endif
    </div>

    <div class="modal fade {{ $form != 'empty' ? 'show' : '' }}" tabindex="-1"
         aria-hidden="true"
         style="{{ $form != 'empty' ? 'display: block;' : '' }}">
        <div class="modal-dialog">
            <form wire:submit="{{ $form }}" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ $form === 'add' ? 'کارمند جدید' : 'ویرایش کارمند' }}
                    </h5>
                    <span wire:click="setForm('empty')" type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </span>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>نام</label>
                        <input type="text" wire:model="name" class="form-control">
                        @error('name')
                            <div class="text-danger text-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>نام خانوادگی</label>
                        <input type="text" wire:model="family" class="form-control">
                        @error('family')
                            <div class="text-danger text-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>موبایل</label>
                        <input type="text" wire:model="phone" class="form-control">
                        @error('phone')
                            <div class="text-danger text-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>کد ملی</label>
                        <input type="text" wire:model="nationalCode" class="form-control">
                        @error('nationalCode')
                            <div class="text-danger text-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>نقش</label>
                        <select wire:model="selectedRole" class="form-control">
                            <option value="">انتخاب نقش</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                        @error('selectedRole')
                            <div class="text-danger text-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <span wire:click="setForm('empty')" type="button" class="btn btn-secondary" data-dismiss="modal">لغو</span>
                    <button class="btn btn-primary">ذخیره</button>
                </div>
            </form>
        </div>
    </div>

    @script
    <script>
        $(document).on("click", ".employees-remove-btn", function () {
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

        Livewire.on("create", event => {
            Toast.fire({
                icon: 'success',
                title: 'کارمند با موفقیت ایجاد شد'
            })
        })

        Livewire.on("edited", event => {
            Toast.fire({
                icon: 'success',
                title: 'اطلاعات کارمند بروزرسانی شد'
            })
        })

        Livewire.on("removed", event => {
            Toast.fire({
                icon: 'success',
                title: 'کارمند حذف شد'
            })
        })
    </script>
    @endscript
</div>
