<div class="section listing-panel">
    <div class="listing-panel-head">
        <h2 class="listing-panel-title">
            <span class="listing-panel-icon">
                <i class="fa fa-pencil"></i>
            </span>
            مدیریت وبلاگ
        </h2>
    </div>

    @if(session('admin_success'))
        <div class="admin-notice success">{{ session('admin_success') }}</div>
    @endif

    <div class="listing-toolbar">
        <div class="listing-toolbar-main listing-toolbar-main--blog">
            <input
                type="text"
                class="listing-search"
                wire:model.live.debounce.300ms="search"
                placeholder="جستجو عنوان"
            >

            <select wire:model.live="category">
                @foreach($categories as $categoryKey => $categoryTitle)
                    <option value="{{ $categoryKey }}">{{ $categoryTitle }}</option>
                @endforeach
            </select>

            <select wire:model.live="status">
                <option value="all">وضعیت</option>
                <option value="published">منتشر شده</option>
                <option value="draft">پیش‌نویس</option>
            </select>
        </div>

        <div class="listing-toolbar-actions">
            <button type="button" class="toolbar-btn toolbar-btn--dark" wire:click="$refresh">فیلتر</button>
            <a href="{{ url('admin/pages') }}" class="toolbar-btn toolbar-btn--success">
                <span>+</span>
                پست جدید
            </a>
        </div>
    </div>

    @if($list->count() > 0)
        <div class="listing-table-wrap">
            <table class="table responsive-table listing-table">
                <thead>
                <tr>
                    <th>عنوان</th>
                    <th>نویسنده</th>
                    <th>دسته‌بندی</th>
                    <th>تاریخ</th>
                    <th>وضعیت</th>
                    <th>عملیات</th>
                </tr>
                </thead>
                <tbody>
                @foreach($list as $item)
                    @php
                        $jalaliDate = $item->created_at ? \Morilog\Jalali\Jalalian::fromDateTime($item->created_at) : null;
                    @endphp
                    <tr>
                        <td data-label="عنوان">
                            <strong class="listing-title">{{ $item->title }}</strong>
                        </td>
                        <td data-label="نویسنده">
                            <strong>{{ $authorName }}</strong>
                        </td>
                        <td data-label="دسته‌بندی">
                            {{ $item->category->name ?? 'بدون دسته‌بندی' }}
                        </td>
                        <td data-label="تاریخ">
                            @if($jalaliDate)
                                {{ convertEnglishToPersianNumbers($jalaliDate->format('%Y/%m/%d')) }}
                            @else
                                -
                            @endif
                        </td>
                        <td data-label="وضعیت">
                            <span class="status-chip {{ (int) $item->status === 1 ? 'active' : 'pending' }}">
                                {{ (int) $item->status === 1 ? 'منتشر شده' : 'پیش‌نویس' }}
                            </span>
                        </td>
                        <td data-label="عملیات">
                            <div class="listing-actions">
                                <a href="{{ url('admin/pages/' . $item->id) }}" class="listing-icon-btn" aria-label="ویرایش">
                                    <i class="fa fa-pencil-square-o"></i>
                                </a>

                                <button
                                    type="button"
                                    class="listing-icon-btn listing-icon-btn--danger"
                                    wire:click="remove({{ $item->id }})"
                                    wire:confirm="از حذف پست {{ $item->title }} اطمینان دارید؟"
                                    aria-label="حذف"
                                >
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="listing-pagination">
            <div class="card">
                <div class="card-body">
                    {{ $list->links('vendor.pagination.default') }}
                </div>
            </div>
        </div>
    @else
        <div class="admin-empty-state">
            <h4>پستی پیدا نشد</h4>
            <p>فیلترها را تغییر دهید یا یک پست جدید بسازید.</p>
        </div>
    @endif
</div>
