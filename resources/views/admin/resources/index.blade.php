@extends('layouts.admin')

@section('title', $config['title'])

@section('content')
    <x-admin.page-shell :title="$config['title']" :icon="$config['icon']" :description="'مدیریت، جستجو، فیلتر و عملیات سریع ' . $config['singular']">
        <x-slot:actions>
            @if(\App\Support\Admin\AdminResourceRegistry::allows($resource, 'create'))
                <a href="{{ route('admin.resources.create', $resource) }}" class="toolbar-btn toolbar-btn--success">
                    <span>+</span>
                    {{ $config['singular'] }} جدید
                </a>
            @endif
        </x-slot:actions>

        <form method="GET" action="{{ route('admin.resources.index', $resource) }}" class="listing-toolbar">
            <div class="listing-toolbar-main">
                <input type="text" name="search" value="{{ request('search') }}" class="listing-search" placeholder="جستجو در {{ $config['title'] }}">

                @if($statusOptions)
                    <select name="status">
                        <option value="">همه وضعیت‌ها</option>
                        @foreach($statusOptions as $value => $label)
                            <option value="{{ $value }}" @selected((string) request('status') === (string) $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                @endif

                @foreach($filters ?? [] as $filter)
                    @if(($filter['type'] ?? 'select') === 'date')
                        <input type="hidden" id="filter_{{ $filter['name'] }}" name="{{ $filter['name'] }}" value="{{ request($filter['name']) }}">
                        <input
                            type="text"
                            name="{{ $filter['name'] }}_display"
                            value="{{ request($filter['name'] . '_display') ?: \App\Support\Admin\PersianDate::formatForDisplay(request($filter['name'])) }}"
                            class="listing-search"
                            placeholder="{{ $filter['label'] }}"
                            data-jalali-input
                            data-target-input="filter_{{ $filter['name'] }}"
                            data-date-type="date"
                            autocomplete="off"
                        >
                    @else
                        <select name="{{ $filter['name'] }}">
                            <option value="">{{ $filter['label'] }}</option>
                            @foreach(($filter['options'] ?? []) as $value => $label)
                                @continue((string) $value === '')
                                <option value="{{ $value }}" @selected((string) request($filter['name']) === (string) $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    @endif
                @endforeach

                <select name="sort">
                    <option value="latest" @selected(request('sort', 'latest') === 'latest')>جدیدترین</option>
                    <option value="oldest" @selected(request('sort') === 'oldest')>قدیمی‌ترین</option>
                    <option value="amount_desc" @selected(request('sort') === 'amount_desc')>بیشترین مبلغ</option>
                    <option value="amount_asc" @selected(request('sort') === 'amount_asc')>کمترین مبلغ</option>
                </select>
            </div>

            <div class="listing-toolbar-actions">
                <button type="submit" class="toolbar-btn toolbar-btn--dark">فیلتر</button>
                <a href="{{ route('admin.resources.index', $resource) }}" class="toolbar-btn toolbar-btn--light">پاک‌سازی</a>
            </div>
        </form>

        <x-admin.table :items="$items" :columns="$config['columns']" :resource="$resource" :config="$config" />
    </x-admin.page-shell>
@endsection
