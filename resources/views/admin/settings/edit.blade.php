@extends('layouts.admin')

@section('title', $pageTitle ?? 'تنظیمات سایت')

@section('content')
    <x-admin.page-shell :title="$pageTitle ?? 'تنظیمات سایت'" :icon="$pageIcon ?? 'fa-sliders'" :description="$pageDescription ?? 'مدیریت تنظیمات سایت.'">
        <form method="POST" action="{{ url()->current() }}" class="settings-form admin-settings-form" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="settings_scope" value="{{ $pageScope ?? 'general' }}">

            @foreach($groups as $groupTitle => $items)
                <div class="settings-group">
                    <h3>{{ $groupTitle }}</h3>
                    <div class="admin-form-grid">
                        @foreach($items as $key => $meta)
                            <div class="admin-form-field">
                                <label for="settings_{{ $key }}">
                                    {{ $meta['label'] }}
                                    @if(!empty($meta['required']))
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>
                                @if(($meta['type'] ?? null) === 'textarea')
                                    <textarea id="settings_{{ $key }}" name="settings[{{ $key }}]" class="form-control" @required(!empty($meta['required']))>{{ old('settings.' . $key, $values[$key] ?? '') }}</textarea>
                                @elseif(($meta['type'] ?? null) === 'select')
                                    <select id="settings_{{ $key }}" name="settings[{{ $key }}]" class="form-control" @required(!empty($meta['required']))>
                                        @foreach(($meta['options'] ?? []) as $optionValue => $optionLabel)
                                            <option value="{{ $optionValue }}" @selected((string) old('settings.' . $key, $values[$key] ?? '') === (string) $optionValue)>{{ $optionLabel }}</option>
                                        @endforeach
                                    </select>
                                @elseif(($meta['type'] ?? null) === 'file')
                                    <input id="settings_{{ $key }}" type="file" name="settings[{{ $key }}]" class="form-control" accept="image/*">
                                    @if(!empty($values[$key]))
                                        <div class="admin-current-image">
                                            <img src="{{ \App\Support\Admin\AdminFileManager::url($values[$key]) }}" alt="{{ $meta['label'] }}">
                                        </div>
                                    @endif
                                @else
                                    <input id="settings_{{ $key }}" type="{{ $meta['type'] ?? 'text' }}" name="settings[{{ $key }}]" value="{{ old('settings.' . $key, $values[$key] ?? '') }}" class="form-control" @required(!empty($meta['required']))>
                                @endif

                                @error('settings.' . $key)
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <div class="admin-form-actions">
                @if(($pageScope ?? null) === 'sms')
                    <button type="submit" name="send_test_sms" value="1" class="btn btn-secondary">ارسال تست</button>
                @endif
                <button type="submit" class="btn btn-primary">{{ $submitLabel ?? 'ذخیره تنظیمات' }}</button>
            </div>
        </form>
    </x-admin.page-shell>
@endsection
