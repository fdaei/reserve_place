<div class="roles-page">
    <div class="section active-section roles-section" id="roles">
        <h3 class="roles-title">
            <i class="fa fa-user-tag"></i>
            مدیریت نقش‌ها
        </h3>

        <form wire:submit="{{ $form === 'edit' ? 'edit' : 'add' }}" class="roles-toolbar">
            <input
                type="text"
                wire:model="name"
                class="form-control roles-main-input"
                placeholder="{{ $form === 'edit' ? 'نام نقش را ویرایش کنید' : 'نام نقش جدید (مثلاً اپراتور تماس)' }}"
            >

            <button type="submit" class="btn btn-primary roles-submit-btn">
                {{ $form === 'edit' ? 'ذخیره تغییرات' : 'افزودن نقش' }}
            </button>

            @if($form === 'edit')
                <button type="button" wire:click="setForm('empty')" class="btn btn-secondary roles-cancel-btn">
                    لغو
                </button>
            @endif
        </form>

        @error('name')
            <div class="text-danger text-error roles-form-error">{{ $message }}</div>
        @enderror

        <div class="roles-table-shell">
            <table class="data-table responsive-table roles-table">
                <thead>
                <tr>
                    <th>شناسه</th>
                    <th>نام نقش</th>
                    <th>تعداد کارکنان</th>
                    <th>تاریخ ایجاد</th>
                    <th>عملیات</th>
                </tr>
                </thead>
                <tbody>
                @forelse($list as $item)
                    @php
                        $gregorianDate = new \DateTime($item->created_at);
                        $jalaliDate = \Morilog\Jalali\Jalalian::fromDateTime($gregorianDate);
                    @endphp
                    <tr>
                        <td data-label="شناسه">{{ $item->id }}</td>
                        <td data-label="نام نقش" class="roles-name-cell">{{ $item->name }}</td>
                        <td data-label="تعداد کارکنان">{{ $item->users_count }}</td>
                        <td data-label="تاریخ ایجاد">{{ $jalaliDate->format('%Y/%m/%d') }}</td>
                        <td data-label="عملیات">
                            <button
                                type="button"
                                class="roles-icon-btn roles-icon-btn--edit"
                                wire:click="setForm('edit', '{{ $item->id }}')"
                                aria-label="ویرایش {{ $item->name }}"
                            >
                                <i class="fa fa-edit"></i>
                            </button>
                            <button
                                type="button"
                                class="roles-icon-btn roles-icon-btn--delete js-remove-role"
                                data-id="{{ $item->id }}"
                                data-title="{{ $item->name }}"
                                aria-label="حذف {{ $item->name }}"
                            >
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="admin-empty-state roles-empty-state">
                                <h4>نقشی ثبت نشده است</h4>
                                <p>از باکس بالای جدول برای ایجاد اولین نقش استفاده کنید.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($list->hasPages())
            <div class="roles-pagination">
                {{ $list->links('vendor.pagination.default') }}
            </div>
        @endif
    </div>

    @script
    <script>
        const roleToast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });

        $(document).on('click', '.js-remove-role', function () {
            let id = $(this).attr('data-id');
            let title = $(this).attr('data-title');

            Swal.fire({
                icon: 'warning',
                title: 'هشدار',
                text: `از حذف کردن ${title} اطمینان دارید؟`,
                confirmButtonText: 'لغو',
                denyButtonText: 'حذف کردن',
                showDenyButton: true,
                background: '#333',
                color: '#fff',
                confirmButtonColor: '#3085d6',
            }).then(res => {
                if (res.isDenied) {
                    Livewire.dispatch('remove', {id: id});
                }
            });
        });

        Livewire.on('create', () => {
            roleToast.fire({icon: 'success', title: 'نقش با موفقیت ایجاد شد'});
        });

        Livewire.on('edited', () => {
            roleToast.fire({icon: 'success', title: 'نقش با موفقیت ویرایش شد'});
        });

        Livewire.on('removed', () => {
            roleToast.fire({icon: 'success', title: 'نقش با موفقیت حذف شد'});
        });
    </script>
    @endscript
</div>
