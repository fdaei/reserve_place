@extends('layouts.admin')

@section('title', 'فوتر و لینک‌ها')

@section('content')
    <x-admin.page-shell title="فوتر و لینک‌ها" icon="fa-link" description="مدیریت متن‌های فوتر و لینک‌های مفید سایت.">
        <form method="POST" action="{{ route('admin.footer-links.texts') }}" class="admin-settings-form">
            @csrf
            @method('PUT')

            <div class="admin-form-grid">
                <div class="admin-form-field admin-form-field--wide">
                    <label for="footer_about_text">متن درباره ما</label>
                    <textarea id="footer_about_text" name="settings[footer_about_text]" class="form-control">{{ old('settings.footer_about_text', $values['footer_about_text'] ?? '') }}</textarea>
                </div>
                <div class="admin-form-field admin-form-field--wide">
                    <label for="footer_contact_text">متن اطلاعات تماس</label>
                    <textarea id="footer_contact_text" name="settings[footer_contact_text]" class="form-control">{{ old('settings.footer_contact_text', $values['footer_contact_text'] ?? '') }}</textarea>
                </div>
                <div class="admin-form-field admin-form-field--wide">
                    <label for="footer_copyright_text">متن کپی‌رایت فوتر</label>
                    <input id="footer_copyright_text" type="text" name="settings[footer_copyright_text]" value="{{ old('settings.footer_copyright_text', $values['footer_copyright_text'] ?? '') }}" class="form-control">
                </div>
            </div>

            <div class="admin-form-actions">
                <button type="submit" class="btn btn-primary">ذخیره متن‌های فوتر</button>
            </div>
        </form>

        <div class="admin-subsection">
            <div class="admin-dashboard-panel-head">
                <h3><i class="fa fa-link"></i> لینک‌های مفید</h3>
            </div>

            <form method="POST" action="{{ route('admin.footer-links.store') }}" class="inline-admin-form">
                @csrf
                <input type="text" name="title" class="form-control" placeholder="عنوان لینک" required>
                <input type="text" name="url" class="form-control" placeholder="آدرس / Slug / URL" required>
                <input type="number" name="sort_order" class="form-control" placeholder="ترتیب" min="0">
                <label class="admin-checkbox-field admin-checkbox-field--compact">
                    <input type="checkbox" name="status" value="1" checked>
                    <span>فعال</span>
                </label>
                <button type="submit" class="toolbar-btn toolbar-btn--success">افزودن لینک</button>
            </form>

            <div class="listing-table-wrap">
                <table class="table listing-table">
                    <thead>
                    <tr>
                        <th>عنوان لینک</th>
                        <th>آدرس / URL</th>
                        <th>ترتیب نمایش</th>
                        <th>وضعیت</th>
                        <th>عملیات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($links as $link)
                        <tr>
                            <td colspan="5">
                                <div class="footer-link-row">
                                    <form method="POST" action="{{ route('admin.footer-links.update', $link) }}" class="footer-link-edit-form">
                                        @csrf
                                        @method('PATCH')
                                        <input type="text" name="title" value="{{ $link->title }}" class="form-control" required>
                                        <input type="text" name="url" value="{{ $link->url }}" class="form-control" required>
                                        <input type="number" name="sort_order" value="{{ $link->sort_order }}" class="form-control" min="0">
                                        <label class="admin-checkbox-field admin-checkbox-field--compact">
                                            <input type="checkbox" name="status" value="1" @checked($link->status)>
                                            <span>فعال</span>
                                        </label>
                                        <button type="submit" class="listing-action-btn listing-action-btn--success">ذخیره</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.footer-links.destroy', $link) }}" data-confirm="این لینک از فوتر حذف شود؟" data-confirm-button="حذف">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="listing-action-btn listing-action-btn--danger">حذف</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </x-admin.page-shell>
@endsection
