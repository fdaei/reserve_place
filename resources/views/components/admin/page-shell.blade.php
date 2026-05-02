@props([
    'title',
    'icon' => 'fa-circle',
    'description' => null,
])

<section {{ $attributes->merge(['class' => 'section listing-panel']) }}>
    <div class="listing-panel-head">
        <div>
            <h2 class="listing-panel-title">
                <span class="listing-panel-icon">
                    <i class="fa {{ $icon }}"></i>
                </span>
                {{ $title }}
            </h2>
            @if($description)
                <p class="admin-page-description">{{ $description }}</p>
            @endif
        </div>

        @isset($actions)
            <div class="pages-head-actions">
                {{ $actions }}
            </div>
        @endisset
    </div>

    {{ $slot }}
</section>
