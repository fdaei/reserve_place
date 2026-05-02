<div class="pages-library">
    <section class="section">
        <div class="admin-section-head">
            <div>
                <h2 class="pages-section-title">
                    <i class="fa fa-files-o"></i>
                    کتابخانه صفحات
                </h2>
                <p>همه صفحات ثابت سایت را از این بخش جستجو، ویرایش یا حذف کنید.</p>
            </div>

            <div class="pages-head-actions">
                <a href="{{ url('admin/pages') }}" class="btn btn-primary">ویرایشگر صفحات</a>
            </div>
        </div>

        <div class="pages-library-toolbar">
            <div class="pages-library-search">
                <label for="pages-search">جستجو در صفحات</label>
                <input
                    id="pages-search"
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    class="form-control"
                    placeholder="جستجو در عنوان یا آدرس صفحه"
                >
            </div>
        </div>

        @if($list->count())
            <div class="pages-library-table-wrap">
                <table class="table responsive-table pages-library-table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>عنوان</th>
                        <th>وضعیت</th>
                        <th>بازدید</th>
                        <th>تاریخ ثبت</th>
                        <th>عملیات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($list as $item)
                        @php
                            $jalaliDate = \Morilog\Jalali\Jalalian::fromCarbon($item->created_at);
                        @endphp
                        <tr>
                            <td data-label="ID">{{ $item->id }}</td>
                            <td data-label="عنوان">
                                <div class="pages-library-title-cell">
                                    <strong>{{ $item->title }}</strong>
                                    <span>{{ $item->url_text }}</span>
                                </div>
                            </td>
                            <td data-label="وضعیت">
                                <span @class(['status-chip', 'active' => (int) $item->status === 1, 'inactive' => (int) $item->status === 0])>
                                    {{ (int) $item->status === 1 ? 'فعال' : 'غیرفعال' }}
                                </span>
                            </td>
                            <td data-label="بازدید">{{ number_format($item->visit_count) }} بازدید</td>
                            <td data-label="تاریخ ثبت">
                                {{ $jalaliDate->format('%Y/%m/%d') }}
                                <br>
                                <span class="op-5">{{ $jalaliDate->format('H:i') }}</span>
                            </td>
                            <td data-label="عملیات">
                                <div class="pages-library-actions">
                                    <a href="{{ url('admin/pages/' . $item->id) }}" class="btn btn-sm btn-primary">ویرایش</a>
                                    <a href="{{ url('/p/' . $item->url_text) }}" class="btn btn-sm btn-secondary" target="_blank" rel="noopener noreferrer">نمایش</a>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="confirmPageRemoval({{ $item->id }}, @js($item->title))">حذف</button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            @if($list->hasPages())
                <div class="pages-library-pagination">
                    {{ $list->links('vendor.pagination.default') }}
                </div>
            @endif
        @else
            <div class="admin-empty-state">
                <h4>صفحه‌ای پیدا نشد</h4>
                <p>عبارت جستجو را تغییر دهید یا یک صفحه جدید بسازید.</p>
            </div>
        @endif
    </section>

    @script
    <script>
        window.confirmPageRemoval = function (id, title) {
            Swal.fire({
                icon: 'warning',
                title: 'حذف صفحه',
                text: `از حذف «${title}» مطمئن هستید؟`,
                showCancelButton: true,
                confirmButtonText: 'حذف صفحه',
                cancelButtonText: 'انصراف',
                confirmButtonColor: '#ef4444',
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('remove', { id: id });
                }
            });
        };

        if (!window.__pagesLibraryToastBound) {
            window.__pagesLibraryToastBound = true;

            Livewire.on('removed', () => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'صفحه با موفقیت حذف شد',
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true,
                });
            });
        }
    </script>
    @endscript
</div>
