@extends('layouts.admin')

@section('title', 'خروجی اکسل')

@section('content')
    <x-admin.page-shell title="خروجی اکسل" icon="fa-file-excel-o" description="دریافت خروجی CSV برای منابع اصلی پنل مدیریت.">
        <form method="POST" action="{{ route('admin.export.store') }}" class="admin-resource-form admin-export-form">
            @csrf
            <div class="admin-form-grid">
                <div class="admin-form-field">
                    <label for="resource">بخش</label>
                    <select id="resource" name="resource" class="form-control" required>
                        @foreach($resources as $resource)
                            <option value="{{ $resource['key'] }}">{{ $resource['title'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="admin-form-field">
                    <label for="format">فرمت</label>
                    <select id="format" name="format" class="form-control" required>
                        <option value="csv">CSV قابل باز شدن در اکسل</option>
                    </select>
                </div>
            </div>

            <div class="admin-form-actions">
                <button class="btn btn-primary">دریافت خروجی</button>
            </div>
        </form>
    </x-admin.page-shell>
@endsection
