{{-- Reusable sidebar navigation link with icon --}}
@props(['active' => false, 'icon' => '', 'href' => '#'])

@php
    // Active state uses accent colour; inactive uses subtle coffee tones
    $classes = $active
        ? 'flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl bg-accent-50 text-accent-600'
        : 'flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl text-coffee-500 hover:bg-cream-100 hover:text-coffee-700 transition-colors duration-200';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon)
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $icon }}"/>
        </svg>
    @endif
    <span>{{ $slot }}</span>
</a>
