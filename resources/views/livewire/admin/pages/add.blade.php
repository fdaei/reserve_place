@php
    $currentPageId = (int) $id;
@endphp

<div class="pages-management">
    <section class="section listing-panel pages-editor-shell">
        <div class="listing-panel-head">
            <h2 class="listing-panel-title">
                <span class="listing-panel-icon">
                    <i class="fa fa-file-text-o"></i>
                </span>
                مدیریت صفحات
            </h2>
        </div>

        @if(session()->has('page_saved'))
            <div class="admin-notice success">{{ session('page_saved') }}</div>
        @endif

        <div class="pages-editor-layout">
            <aside class="pages-sidebar-card">
                <div class="pages-list-scroll">
                    <button
                        type="button"
                        wire:click="startCreating"
                        @class(['pages-menu-item', 'active' => $currentPageId === 0])
                    >
                        صفحه جدید
                    </button>

                    @forelse($pages as $page)
                        <button
                            type="button"
                            wire:click="selectPage({{ $page->id }})"
                            @class(['pages-menu-item', 'active' => $currentPageId === (int) $page->id])
                        >
                            {{ $page->title }}
                        </button>
                    @empty
                        <div class="admin-empty-state">
                            <h4>صفحه‌ای ثبت نشده است</h4>
                            <p>برای شروع، اولین صفحه را ایجاد کنید.</p>
                        </div>
                    @endforelse
                </div>
            </aside>

            <form wire:submit="update" class="pages-editor-form pages-editor-form--simple">
                <div class="pages-editor-fields">
                    <div class="pages-inline-field">
                        <input
                            id="page-title"
                            wire:model="title"
                            type="text"
                            class="form-control"
                            placeholder="عنوان صفحه"
                            required
                        >
                        @error('title')
                            <div class="text-danger text-error">{{ $message }}</div>
                        @enderror
                        @error('urlTitle')
                            <div class="text-danger text-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="pages-inline-field pages-inline-field--textarea">
                        <textarea
                            id="page-content"
                            wire:model="text"
                            class="form-control pages-editor-textarea"
                            required
                            placeholder="محتوای صفحه"
                        ></textarea>
                        @error('text')
                            <div class="text-danger text-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="pages-actions">
                        <button type="submit" class="btn btn-primary pages-submit-btn" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="update">ذخیره صفحه</span>
                            <span wire:loading wire:target="update">در حال ذخیره...</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>
