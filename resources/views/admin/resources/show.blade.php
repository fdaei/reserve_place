@extends('layouts.admin')

@section('title', 'جزئیات ' . $config['singular'])

@section('content')
    <x-admin.page-shell :title="'جزئیات ' . $config['singular']" :icon="$config['icon']">
        <x-slot:actions>
            @if(\App\Support\Admin\AdminResourceRegistry::allows($resource, 'edit'))
                <a href="{{ route('admin.resources.edit', [$resource, $item->id]) }}" class="toolbar-btn toolbar-btn--dark">ویرایش</a>
            @endif
            <a href="{{ route('admin.resources.index', $resource) }}" class="toolbar-btn toolbar-btn--light">بازگشت</a>
        </x-slot:actions>

        <div class="admin-detail-grid">
            @foreach($fields as $field)
                @php
                    $column = ['key' => $field['name'], 'label' => $field['label'], 'type' => $field['type'] === 'checkbox' ? 'boolean' : ($field['type'] ?? 'text')];
                    $fileUrl = ($field['type'] ?? null) === 'file' ? \App\Support\Admin\AdminFileManager::url(data_get($item, $field['name']), $field['disk'] ?? 'public', $field['directory'] ?? null) : null;
                    $selectedValues = isset($field['value']) && is_callable($field['value']) ? (array) $field['value']($item) : [];
                @endphp
                <div class="admin-detail-item">
                    <span>{{ $field['label'] }}</span>
                    @if(($field['type'] ?? null) === 'file' && $fileUrl)
                        <strong><img src="{{ $fileUrl }}" alt="{{ $field['label'] }}" class="admin-detail-image"></strong>
                    @elseif(($field['type'] ?? null) === 'checkbox-group')
                        <strong>
                            @forelse($selectedValues as $selectedValue)
                                <span class="badge badge-info">{{ data_get($field['options'] ?? [], $selectedValue, $selectedValue) }}</span>
                            @empty
                                -
                            @endforelse
                        </strong>
                    @else
                        <strong>{{ \App\Support\Admin\AdminResourceRegistry::displayValue($item, $column) }}</strong>
                    @endif
                </div>
            @endforeach
        </div>
    </x-admin.page-shell>
@endsection
