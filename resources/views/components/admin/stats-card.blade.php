@props([
    'title',
    'value',
    'meta' => null,
    'icon' => 'fa-circle',
    'tone' => 'blue',
])

<article class="stat-card admin-stat-card admin-stat-card--{{ $tone }}">
    <div class="stat-header">
        <h4>{{ $title }}</h4>
        <span class="admin-stat-icon">
            <i class="fa {{ $icon }}"></i>
        </span>
    </div>
    <p class="stat-number">{{ is_numeric($value) ? number_format((int) $value) : $value }}</p>
    @if($meta)
        <div class="stat-footer">
            <span>{{ $meta }}</span>
        </div>
    @endif
</article>
