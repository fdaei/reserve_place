@props(['value'])

@php
    $class = \App\Support\Admin\AdminResourceRegistry::statusClass($value);
    $label = \App\Support\Admin\AdminResourceRegistry::statusLabel($value);
@endphp

<span {{ $attributes->merge(['class' => 'status-chip ' . $class]) }}>{{ $label }}</span>
