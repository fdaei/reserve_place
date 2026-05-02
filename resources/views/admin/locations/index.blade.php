@extends('layouts.admin')

@section('title', 'شهرها و استان‌ها')

@section('content')
    <x-admin.page-shell title="شهرها و استان‌ها" icon="fa-map" description="مدیریت یکپارچه استان‌ها، شهرها و شهرهای محبوب.">
        <div class="admin-tabs">
            <a href="#provinces" class="admin-tab">استان‌ها</a>
            <a href="#cities" class="admin-tab">شهرها</a>
            <a href="#popular-cities" class="admin-tab">شهرهای محبوب</a>
        </div>

        <div id="provinces" class="admin-subsection">
            <div class="admin-dashboard-panel-head">
                <h3><i class="fa fa-map-marker"></i> استان‌ها</h3>
                <a href="{{ route('admin.resources.create', 'provinces') }}" class="toolbar-btn toolbar-btn--success">استان جدید</a>
            </div>
            <div class="listing-table-wrap">
                <table class="table listing-table">
                    <thead>
                    <tr>
                        <th>نام استان</th>
                        <th>تعداد شهرها</th>
                        <th>تعداد اقامتگاه‌ها</th>
                        <th>بنر استان</th>
                        <th>وضعیت</th>
                        <th>عملیات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($provinces as $province)
                        <tr>
                            <td>{{ $province->name }}</td>
                            <td>{{ number_format($province->cities_count) }}</td>
                            <td>{{ number_format($province->residences_count) }}</td>
                            <td>
                                @if($url = \App\Support\Admin\AdminFileManager::url($province->banner_image))
                                    <img src="{{ $url }}" alt="{{ $province->name }}" class="admin-table-thumb">
                                @else
                                    -
                                @endif
                            </td>
                            <td><x-admin.status-badge :value="$province->is_use" /></td>
                            <td><a href="{{ route('admin.resources.edit', ['provinces', $province->id]) }}" class="listing-action-btn listing-action-btn--info">ویرایش</a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            {{ $provinces->links('vendor.pagination.bootstrap-4') }}
        </div>

        <div id="cities" class="admin-subsection">
            <div class="admin-dashboard-panel-head">
                <h3><i class="fa fa-map-pin"></i> شهرها</h3>
                <a href="{{ route('admin.resources.create', 'cities') }}" class="toolbar-btn toolbar-btn--success">شهر جدید</a>
            </div>
            <div class="listing-table-wrap">
                <table class="table listing-table">
                    <thead>
                    <tr>
                        <th>نام شهر</th>
                        <th>استان</th>
                        <th>تعداد اقامتگاه‌ها</th>
                        <th>تصویر / بنر شهر</th>
                        <th>وضعیت</th>
                        <th>عملیات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($cities as $city)
                        <tr>
                            <td>{{ $city->name }}</td>
                            <td>{{ $city->province?->name ?: '-' }}</td>
                            <td>{{ number_format($city->residences_count) }}</td>
                            <td>
                                @if($url = \App\Support\Admin\AdminFileManager::url($city->banner_image))
                                    <img src="{{ $url }}" alt="{{ $city->name }}" class="admin-table-thumb">
                                @else
                                    -
                                @endif
                            </td>
                            <td><x-admin.status-badge :value="$city->is_use" /></td>
                            <td><a href="{{ route('admin.resources.edit', ['cities', $city->id]) }}" class="listing-action-btn listing-action-btn--info">ویرایش</a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            {{ $cities->links('vendor.pagination.bootstrap-4') }}
        </div>

        <div id="popular-cities" class="admin-subsection">
            <div class="admin-dashboard-panel-head">
                <h3><i class="fa fa-star"></i> شهرهای محبوب</h3>
                <a href="{{ route('admin.resources.create', 'popular-cities') }}" class="toolbar-btn toolbar-btn--success">شهر محبوب جدید</a>
            </div>
            <div class="listing-table-wrap">
                <table class="table listing-table">
                    <thead>
                    <tr>
                        <th>شهر</th>
                        <th>استان</th>
                        <th>تصویر</th>
                        <th>ترتیب نمایش</th>
                        <th>وضعیت</th>
                        <th>عملیات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($popularCities as $popularCity)
                        <tr>
                            <td>{{ $popularCity->city?->name ?: '-' }}</td>
                            <td>{{ $popularCity->city?->province?->name ?: '-' }}</td>
                            <td>
                                @if($url = \App\Support\Admin\AdminFileManager::url($popularCity->image_path))
                                    <img src="{{ $url }}" alt="{{ $popularCity->city?->name }}" class="admin-table-thumb">
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ number_format($popularCity->sort_order) }}</td>
                            <td><x-admin.status-badge :value="$popularCity->status" /></td>
                            <td><a href="{{ route('admin.resources.edit', ['popular-cities', $popularCity->id]) }}" class="listing-action-btn listing-action-btn--info">ویرایش</a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            {{ $popularCities->links('vendor.pagination.bootstrap-4') }}
        </div>
    </x-admin.page-shell>
@endsection
