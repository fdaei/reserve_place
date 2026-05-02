@extends('layouts.admin')

@section('title', 'اتصال نقش به کاربر پنل')

@section('content')
    <x-admin.page-shell title="اتصال نقش به کاربر پنل" icon="fa-users" description="برای کارمندان و میزبان‌ها، نقش‌های مجاز پنل مدیریت را تعیین کنید.">
        <div class="listing-table-wrap">
            <table class="table responsive-table listing-table">
                <thead>
                <tr>
                    <th>کاربر</th>
                    <th>موبایل</th>
                    <th>نقش‌های فعلی</th>
                    <th>ویرایش نقش</th>
                </tr>
                </thead>
                <tbody>
                @forelse($users as $user)
                    <tr>
                        <td data-label="کاربر"><strong>{{ $user->full_name }}</strong></td>
                        <td data-label="موبایل"><span class="listing-phone">{{ $user->phone }}</span></td>
                        <td data-label="نقش‌های فعلی">
                            <div class="access-role-pills">
                                @forelse($user->roles as $role)
                                    <span class="badge badge-info">{{ $role->name }}</span>
                                @empty
                                    <span class="badge badge-warning">بدون نقش</span>
                                @endforelse
                            </div>
                        </td>
                        <td data-label="ویرایش نقش">
                            <form method="POST" action="{{ route('admin.role-assign.store') }}" class="role-inline-form">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $user->id }}">
                                <div class="role-checkbox-grid">
                                    @foreach($roles as $role)
                                        <label class="admin-checkbox-field">
                                            <input type="checkbox" name="roles[]" value="{{ $role->id }}" @checked($user->roles->contains($role))>
                                            <span>{{ $role->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <button class="btn btn-sm btn-primary">ذخیره</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">کاربر پنلی یا میزبان قدیمی یافت نشد.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="listing-pagination">
            <div class="card">
                <div class="card-body">{{ $users->links('vendor.pagination.bootstrap-4') }}</div>
            </div>
        </div>
    </x-admin.page-shell>
@endsection
