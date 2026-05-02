@push('head')
    <style>
        .locations-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 16px;
            border-bottom: 1px solid var(--admin-border);
        }

        .locations-tab {
            min-height: 42px;
            padding: 0 16px;
            border: 1px solid transparent;
            border-bottom: 0;
            border-radius: 8px 8px 0 0;
            background: transparent;
            color: var(--admin-muted);
            font-weight: 700;
        }

        .locations-tab.is-active {
            background: #fff;
            border-color: var(--admin-border);
            color: var(--admin-primary);
        }

        .location-order-input {
            width: 90px;
            min-height: 36px;
            text-align: center;
        }
    </style>
@endpush

<div class="section listing-panel">
    <div class="listing-panel-head">
        <div>
            <h2 class="listing-panel-title">
                <span class="listing-panel-icon"><i class="fa fa-map"></i></span>
                شهرها و استان‌ها
            </h2>
            <p class="admin-page-description">مدیریت تفکیک‌شده استان‌ها، شهرها و شهرهای محبوب.</p>
        </div>
    </div>

    <div class="locations-tabs" role="tablist">
        <button type="button" @class(['locations-tab', 'is-active' => $activeTab === 'provinces']) wire:click="setTab('provinces')">استان‌ها</button>
        <button type="button" @class(['locations-tab', 'is-active' => $activeTab === 'cities']) wire:click="setTab('cities')">شهرها</button>
        <button type="button" @class(['locations-tab', 'is-active' => $activeTab === 'popular']) wire:click="setTab('popular')">شهرهای محبوب</button>
    </div>

    @if($activeTab === 'provinces')
        <div class="listing-toolbar">
            <div class="listing-toolbar-main">
                <input type="text" wire:model.live.debounce.300ms="provinceSearch" class="listing-search" placeholder="جستجوی استان">
                <select wire:model.live="provinceStatus">
                    <option value="all">همه وضعیت‌ها</option>
                    <option value="1">فعال</option>
                    <option value="0">غیرفعال</option>
                </select>
            </div>
            <div class="listing-toolbar-actions">
                <a href="{{ route('admin.resources.create', 'provinces') }}" class="toolbar-btn toolbar-btn--success"><span>+</span> استان جدید</a>
            </div>
        </div>

        <div class="listing-table-wrap">
            <table class="table responsive-table listing-table">
                <thead>
                <tr>
                    <th>استان</th>
                    <th>کشور</th>
                    <th>شهرها</th>
                    <th>اقامتگاه‌ها</th>
                    <th>بنر</th>
                    <th>وضعیت</th>
                    <th>عملیات</th>
                </tr>
                </thead>
                <tbody>
                @forelse($provinces as $province)
                    <tr>
                        <td data-label="استان">{{ $province->name }}</td>
                        <td data-label="کشور">{{ $province->country?->name ?: '-' }}</td>
                        <td data-label="شهرها">{{ number_format($province->cities_count) }}</td>
                        <td data-label="اقامتگاه‌ها">{{ number_format($province->residences_count) }}</td>
                        <td data-label="بنر">
                            @if($url = \App\Support\Admin\AdminFileManager::url($province->banner_image))
                                <img src="{{ $url }}" alt="{{ $province->name }}" class="admin-table-thumb">
                            @else
                                -
                            @endif
                        </td>
                        <td data-label="وضعیت"><x-admin.status-badge :value="$province->is_use" /></td>
                        <td data-label="عملیات">
                            <div class="listing-actions">
                                <button type="button" class="listing-action-btn listing-action-btn--info" wire:click="toggleProvince({{ $province->id }})">تغییر وضعیت</button>
                                <a href="{{ route('admin.resources.edit', ['provinces', $province->id]) }}" class="listing-icon-btn" title="ویرایش"><i class="fa fa-pencil-square-o"></i></a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7"><x-admin.empty-state /></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="listing-pagination"><div class="card"><div class="card-body">{{ $provinces->links('vendor.pagination.bootstrap-4') }}</div></div></div>
    @endif

    @if($activeTab === 'cities')
        <div class="listing-toolbar">
            <div class="listing-toolbar-main">
                <input type="text" wire:model.live.debounce.300ms="citySearch" class="listing-search" placeholder="جستجوی شهر">
                <select wire:model.live="cityProvince">
                    <option value="all">همه استان‌ها</option>
                    @foreach($provinceOptions as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
                <select wire:model.live="cityStatus">
                    <option value="all">همه وضعیت‌ها</option>
                    <option value="1">فعال</option>
                    <option value="0">غیرفعال</option>
                </select>
            </div>
            <div class="listing-toolbar-actions">
                <a href="{{ route('admin.resources.create', 'cities') }}" class="toolbar-btn toolbar-btn--success"><span>+</span> شهر جدید</a>
            </div>
        </div>

        <div class="listing-table-wrap">
            <table class="table responsive-table listing-table">
                <thead>
                <tr>
                    <th>شهر</th>
                    <th>استان</th>
                    <th>اقامتگاه‌ها</th>
                    <th>بنر</th>
                    <th>وضعیت</th>
                    <th>عملیات</th>
                </tr>
                </thead>
                <tbody>
                @forelse($cities as $city)
                    <tr>
                        <td data-label="شهر">{{ $city->name }}</td>
                        <td data-label="استان">{{ $city->province?->name ?: '-' }}</td>
                        <td data-label="اقامتگاه‌ها">{{ number_format($city->residences_count) }}</td>
                        <td data-label="بنر">
                            @if($url = \App\Support\Admin\AdminFileManager::url($city->banner_image))
                                <img src="{{ $url }}" alt="{{ $city->name }}" class="admin-table-thumb">
                            @else
                                -
                            @endif
                        </td>
                        <td data-label="وضعیت"><x-admin.status-badge :value="$city->is_use" /></td>
                        <td data-label="عملیات">
                            <div class="listing-actions">
                                <button type="button" class="listing-action-btn listing-action-btn--info" wire:click="toggleCity({{ $city->id }})">تغییر وضعیت</button>
                                <a href="{{ route('admin.resources.edit', ['cities', $city->id]) }}" class="listing-icon-btn" title="ویرایش"><i class="fa fa-pencil-square-o"></i></a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6"><x-admin.empty-state /></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="listing-pagination"><div class="card"><div class="card-body">{{ $cities->links('vendor.pagination.bootstrap-4') }}</div></div></div>
    @endif

    @if($activeTab === 'popular')
        <div class="listing-toolbar">
            <div class="listing-toolbar-main">
                <input type="text" wire:model.live.debounce.300ms="popularSearch" class="listing-search" placeholder="جستجوی شهر محبوب">
                <select wire:model.live="popularProvince">
                    <option value="all">همه استان‌ها</option>
                    @foreach($provinceOptions as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
                <select wire:model.live="popularStatus">
                    <option value="all">همه وضعیت‌ها</option>
                    <option value="1">فعال</option>
                    <option value="0">غیرفعال</option>
                </select>
            </div>
            <div class="listing-toolbar-actions">
                <button type="button" class="toolbar-btn toolbar-btn--dark" wire:click="savePopularOrder">ذخیره ترتیب</button>
                <a href="{{ route('admin.resources.create', 'popular-cities') }}" class="toolbar-btn toolbar-btn--success"><span>+</span> شهر محبوب جدید</a>
            </div>
        </div>

        <div class="listing-table-wrap">
            <table class="table responsive-table listing-table">
                <thead>
                <tr>
                    <th>شهر</th>
                    <th>استان</th>
                    <th>تصویر</th>
                    <th>ترتیب</th>
                    <th>وضعیت</th>
                    <th>عملیات</th>
                </tr>
                </thead>
                <tbody>
                @forelse($popularCities as $popularCity)
                    <tr>
                        <td data-label="شهر">{{ $popularCity->city?->name ?: '-' }}</td>
                        <td data-label="استان">{{ $popularCity->city?->province?->name ?: '-' }}</td>
                        <td data-label="تصویر">
                            @if($url = \App\Support\Admin\AdminFileManager::url($popularCity->image_path))
                                <img src="{{ $url }}" alt="{{ $popularCity->city?->name }}" class="admin-table-thumb">
                            @else
                                -
                            @endif
                        </td>
                        <td data-label="ترتیب">
                            <input type="text" class="form-control location-order-input" wire:model.defer="popularOrders.{{ $popularCity->id }}">
                        </td>
                        <td data-label="وضعیت"><x-admin.status-badge :value="$popularCity->status" /></td>
                        <td data-label="عملیات">
                            <div class="listing-actions">
                                <button type="button" class="listing-action-btn listing-action-btn--info" wire:click="togglePopularCity({{ $popularCity->id }})">تغییر وضعیت</button>
                                <a href="{{ route('admin.resources.edit', ['popular-cities', $popularCity->id]) }}" class="listing-icon-btn" title="ویرایش"><i class="fa fa-pencil-square-o"></i></a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6"><x-admin.empty-state /></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="listing-pagination"><div class="card"><div class="card-body">{{ $popularCities->links('vendor.pagination.bootstrap-4') }}</div></div></div>
    @endif

    @script
    <script>
        Livewire.on('locations-saved', () => {
            Toast.fire({ icon: 'success', title: 'اطلاعات موقعیت‌ها ذخیره شد' });
        });
    </script>
    @endscript
</div>
