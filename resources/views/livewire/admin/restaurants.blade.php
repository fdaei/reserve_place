<div class="section listing-panel">
    <div class="listing-panel-head">
        <h2 class="listing-panel-title">
            <span class="listing-panel-icon">
                <i class="fa fa-cutlery"></i>
            </span>
            مدیریت کافه و رستوران
        </h2>
    </div>

    @if(session('admin_success'))
        <div class="admin-notice success">{{ session('admin_success') }}</div>
    @endif

    <div class="listing-toolbar">
        <div class="listing-toolbar-main">
            <input
                type="text"
                class="listing-search"
                wire:model.live.debounce.300ms="search"
                placeholder="جستجو نام"
            >

            <select wire:model.live="city">
                <option value="0">همه شهرها</option>
                @foreach($cities as $cityItem)
                    <option value="{{ $cityItem->id }}">{{ $cityItem->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="incomeModel">
                <option value="all">مدل درآمدی</option>
                <option value="paid">دلاری</option>
                <option value="free">رایگان</option>
            </select>

            <select wire:model.live="status">
                <option value="all">همه وضعیت‌ها</option>
                <option value="active">فعال</option>
                <option value="pending">در انتظار</option>
            </select>
        </div>

        <div class="listing-toolbar-actions">
            <select class="listing-sort-select" wire:model.live="sort" aria-label="مرتب‌سازی">
                <option value="latest">جدید</option>
                <option value="oldest">قدیم</option>
                <option value="popular">پربازدید</option>
            </select>

            <button type="button" class="toolbar-btn toolbar-btn--dark" wire:click="$refresh">فیلتر</button>
            <a href="{{ url('add-foodstore') }}" class="toolbar-btn toolbar-btn--success">
                <span>+</span>
                جدید
            </a>
        </div>
    </div>

    @if($list->count() > 0)
        <div class="listing-table-wrap">
            <table class="table responsive-table listing-table">
                <thead>
                <tr>
                    <th>نام</th>
                    <th>صاحب</th>
                    <th>شهر</th>
                    <th>مدل درآمدی</th>
                    <th>نوع</th>
                    <th>وضعیت</th>
                    <th>عملیات</th>
                </tr>
                </thead>
                <tbody>
                @foreach($list as $item)
                    @php
                        $hostName = trim(($item->admin->name ?? '') . ' ' . ($item->admin->family ?? ''));
                        $incomeLabel = (int) $item->vip === 1 ? 'دلاری' : 'رایگان';
                        $incomeClass = (int) $item->vip === 1 ? 'listing-badge--income-paid' : 'listing-badge--income-free';
                    @endphp
                    <tr>
                        <td data-label="نام">
                            <strong class="listing-title">{{ $item->title }}</strong>
                        </td>
                        <td data-label="صاحب">
                            <strong>{{ $hostName !== '' ? $hostName : 'بدون نام' }}</strong>
                        </td>
                        <td data-label="شهر">
                            {{ $item->city->name ?? '-' }}
                        </td>
                        <td data-label="مدل درآمدی">
                            <span class="listing-badge {{ $incomeClass }}">{{ $incomeLabel }}</span>
                        </td>
                        <td data-label="نوع">
                            {{ \App\Models\FoodStore::getStoreType($item->store_type) ?? 'رستوران' }}
                        </td>
                        <td data-label="وضعیت">
                            <span class="status-chip {{ (int) $item->status === 1 ? 'active' : 'pending' }}">
                                {{ (int) $item->status === 1 ? 'فعال' : 'در انتظار' }}
                            </span>
                        </td>
                        <td data-label="عملیات">
                            <div class="listing-actions">
                                @if((int) $item->status !== 1)
                                    <button type="button" class="listing-icon-btn listing-icon-btn--success" wire:click="approve({{ $item->id }})" aria-label="تایید">
                                        <i class="fa fa-check-square"></i>
                                    </button>
                                @endif

                                <button type="button" class="listing-icon-btn" wire:click="edit({{ $item->id }})" aria-label="ویرایش">
                                    <i class="fa fa-pencil-square-o"></i>
                                </button>

                                <button
                                    type="button"
                                    class="listing-icon-btn listing-icon-btn--danger"
                                    wire:click="remove({{ $item->id }})"
                                    wire:confirm="از حذف {{ $item->title }} اطمینان دارید؟"
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
            <h4>رستورانی پیدا نشد</h4>
            <p>فیلترها را تغییر دهید یا جستجو را پاک کنید.</p>
        </div>
    @endif
</div>
