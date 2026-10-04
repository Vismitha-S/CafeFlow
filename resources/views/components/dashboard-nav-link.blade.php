{{-- Reusable sidebar navigation link with icon --}}
@props(['active' => false, 'icon' => '', 'href' => '#'])

@php
    // Active state uses a warm cafe pill highlight; inactive uses espresso-tinted muted tones
    $classes = $active
        ? 'flex items-center gap-3 px-3.5 py-2.5 text-sm font-semibold rounded-xl bg-cream-200/70 text-coffee-900 shadow-xs border border-cream-300/60 transition-all duration-200'
        : 'flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl text-coffee-600 hover:bg-cream-100 hover:text-coffee-900 transition-all duration-200';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon)
        <svg class="w-5 h-5 shrink-0 {{ $active ? 'text-accent-600' : 'text-coffee-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="{{ $icon }}"/>
        </svg>
    @endif
    <span class="tracking-tight">{{ $slot }}</span>
</a>
