@extends('layouts.admin')

@section('title', ($item ? 'ویرایش ' : 'ایجاد ') . $config['singular'])

@section('content')
    <x-admin.page-shell :title="($item ? 'ویرایش ' : 'ایجاد ') . $config['singular']" :icon="$config['icon']">
        <form method="POST" action="{{ $item ? route('admin.resources.update', [$resource, $item->id]) : route('admin.resources.store', $resource) }}" class="admin-resource-form" enctype="multipart/form-data">
            @csrf
            @if($item)
                @method('PUT')
            @endif

            <div class="admin-form-grid">
                @foreach($fields as $field)
                    <x-admin.field :field="$field" :item="$item" />
                @endforeach
            </div>

            <div class="admin-form-actions">
                <a href="{{ route('admin.resources.index', $resource) }}" class="btn btn-secondary">بازگشت</a>
                <button type="submit" class="btn btn-primary">ذخیره</button>
            </div>
        </form>
    </x-admin.page-shell>
@endsection
