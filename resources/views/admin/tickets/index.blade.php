@extends('layouts.admin')

@section('title', 'تیکت‌ها')

@section('content')
    <x-admin.page-shell title="تیکت‌ها" icon="fa-ticket" description="مدیریت پیام‌های پشتیبانی و پیگیری وضعیت پاسخگویی.">
        <form method="GET" action="{{ route('admin.tickets.index') }}" class="listing-toolbar">
            <div class="listing-toolbar-main">
                <input type="text" name="search" value="{{ request('search') }}" class="listing-search" placeholder="جستجو عنوان یا شناسه">
                <select name="status">
                    <option value="">همه وضعیت‌ها</option>
                    <option value="0" @selected(request('status') === '0')>باز</option>
                    <option value="1" @selected(request('status') === '1')>پاسخ داده شده</option>
                    <option value="2" @selected(request('status') === '2')>بسته شده</option>
                </select>
            </div>
            <div class="listing-toolbar-actions">
                <button type="submit" class="toolbar-btn toolbar-btn--dark">فیلتر</button>
            </div>
        </form>

        @if($tickets->count())
            <div class="listing-table-wrap">
                <table class="table responsive-table listing-table">
                    <thead>
                    <tr>
                        <th>عنوان</th>
                        <th>کاربر</th>
                        <th>پیام‌ها</th>
                        <th>خوانده نشده</th>
                        <th>وضعیت</th>
                        <th>عملیات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($tickets as $ticket)
                        <tr>
                            <td data-label="عنوان"><strong>{{ $ticket->title }}</strong></td>
                            <td data-label="کاربر">{{ data_get($ticket, 'user.name', 'کاربر') }} {{ data_get($ticket, 'user.family') }}</td>
                            <td data-label="پیام‌ها">{{ number_format($ticket->chats_count) }}</td>
                            <td data-label="خوانده نشده">{{ number_format($ticket->unseen_count) }}</td>
                            <td data-label="وضعیت"><x-admin.status-badge :value="$ticket->status" /></td>
                            <td data-label="عملیات">
                                <div class="listing-actions">
                                    <a href="{{ route('admin.tickets.show', $ticket) }}" class="listing-icon-btn" aria-label="مشاهده">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.tickets.status', $ticket) }}" class="inline-delete-form">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="2">
                                        <button class="listing-icon-btn" title="بستن" aria-label="بستن">
                                            <i class="fa fa-lock"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="listing-pagination">
                <div class="card">
                    <div class="card-body">{{ $tickets->links('vendor.pagination.bootstrap-4') }}</div>
                </div>
            </div>
        @else
            <x-admin.empty-state title="تیکتی پیدا نشد" description="با تغییر فیلترها یا دریافت پیام جدید این بخش تکمیل می‌شود." />
        @endif
    </x-admin.page-shell>
@endsection
