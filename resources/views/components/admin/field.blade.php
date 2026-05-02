@props([
    'field',
    'item' => null,
])

@php
    $name = $field['name'];
    $type = $field['type'] ?? 'text';
    $label = $field['label'] ?? $name;
    $required = !empty($field['required']);
    $baseValue = $item && isset($field['value']) && is_callable($field['value'])
        ? $field['value']($item)
        : ($item ? data_get($item, $name) : request($name, $field['default'] ?? null));
    $value = old($name, $baseValue);

    if ($value instanceof \Carbon\CarbonInterface) {
        $value = match ($type) {
            'datetime-local' => $value->format('Y-m-d\TH:i'),
            'date' => $value->format('Y-m-d'),
            'time' => $value->format('H:i'),
            default => (string) $value,
        };
    }

    if ($name === 'national_code') {
        $value = preg_replace('/\D+/', '', convertPersianToEnglishNumbers((string) $value));
    }

    $displayDateValue = in_array($type, ['date', 'datetime-local'], true)
        ? old($name . '_display', \App\Support\Admin\PersianDate::formatForDisplay($baseValue, $type === 'datetime-local'))
        : null;

    $moneyDisplayValue = $type === 'money' && filled($value)
        ? number_format((int) $value)
        : old($name . '_display');

    $currentFileUrl = $type === 'file' && $item
        ? \App\Support\Admin\AdminFileManager::url(data_get($item, $name), $field['disk'] ?? 'public', $field['directory'] ?? null)
        : null;

    $selectedValues = collect(old($name, is_array($value) ? $value : []))->filter(fn ($selected) => $selected !== '')->map(fn ($selected) => (string) $selected)->all();
@endphp

<div @class(['admin-form-field', 'admin-form-field--wide' => (($field['span'] ?? 1) === 2)])>
    @if($type === 'checkbox')
        <input type="hidden" name="{{ $name }}" value="0">
        <label class="admin-checkbox-field">
            <input type="checkbox" name="{{ $name }}" value="1" @checked((bool) $value)>
            <span>{{ $label }}</span>
        </label>
    @elseif($type === 'checkbox-group')
        <label>{{ $label }}</label>
        <div class="role-checkbox-grid">
            @foreach(($field['options'] ?? []) as $optionValue => $optionLabel)
                @continue((string) $optionValue === '')
                <label class="admin-checkbox-field">
                    <input type="checkbox" name="{{ $name }}[]" value="{{ $optionValue }}" @checked(in_array((string) $optionValue, $selectedValues, true))>
                    <span>{{ $optionLabel }}</span>
                </label>
            @endforeach
        </div>
    @else
        <label for="{{ $name }}">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>

        @if($type === 'textarea')
            <textarea id="{{ $name }}" name="{{ $name }}" class="form-control" @required($required)>{{ $value }}</textarea>
        @elseif($type === 'select')
            <select id="{{ $name }}" name="{{ $name }}" class="form-control" @required($required)>
                @foreach(($field['options'] ?? []) as $optionValue => $optionLabel)
                    <option value="{{ $optionValue }}" @selected((string) $value === (string) $optionValue)>{{ $optionLabel }}</option>
                @endforeach
            </select>
        @elseif(in_array($type, ['date', 'datetime-local'], true))
            <input type="hidden" id="{{ $name }}" name="{{ $name }}" value="{{ old($name, $value) }}">
            <input
                id="{{ $name }}_display"
                type="text"
                name="{{ $name }}_display"
                value="{{ $displayDateValue }}"
                class="form-control"
                @required($required)
                data-jalali-input
                data-target-input="{{ $name }}"
                data-date-type="{{ $type }}"
                data-min-source="{{ $field['min_source'] ?? '' }}"
                autocomplete="off"
                placeholder="{{ $type === 'datetime-local' ? 'مثال: 1405/02/08 14:30' : 'مثال: 1405/02/08' }}"
            >
        @elseif($type === 'money')
            <input type="hidden" id="{{ $name }}" name="{{ $name }}" value="{{ old($name, $value) }}">
            <input
                id="{{ $name }}_display"
                type="text"
                name="{{ $name }}_display"
                value="{{ $moneyDisplayValue }}"
                class="form-control"
                @required($required)
                data-money-input
                data-target-input="{{ $name }}"
                inputmode="numeric"
                autocomplete="off"
            >
        @elseif($type === 'file')
            <input id="{{ $name }}" type="file" name="{{ $name }}" class="form-control" @required($required && !$currentFileUrl) accept="{{ $field['accept'] ?? 'image/*' }}">

            @if($currentFileUrl)
                <div class="admin-current-image">
                    <img src="{{ $currentFileUrl }}" alt="{{ $label }}">
                </div>
            @endif
        @else
            <input
                id="{{ $name }}"
                type="{{ $type }}"
                name="{{ $name }}"
                value="{{ $value }}"
                class="form-control"
                @required($required)
                @if(!empty($field['inputmode'])) inputmode="{{ $field['inputmode'] }}" @endif
                @if(!empty($field['maxlength'])) maxlength="{{ $field['maxlength'] }}" @endif
            >
        @endif
    @endif

    @if(!empty($field['help']))
        <small>{{ $field['help'] }}</small>
    @endif

    @error($name)
        <small class="text-danger">{{ $message }}</small>
    @enderror

    @error($name . '_display')
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>
