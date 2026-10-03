{{-- Reusable dashboard stat card for KPI display --}}
@props([
    'title' => '',
    'value' => '0',
    'icon' => '',
    'trend' => null,
    'trendUp' => true,
    'color' => 'accent',
])

@php
    // Map colour names to Tailwind bg/text class pairs
    $colorMap = [
        'accent' => ['bg' => 'bg-accent-50', 'text' => 'text-accent-500'],
        'coffee' => ['bg' => 'bg-coffee-50', 'text' => 'text-coffee-500'],
        'emerald' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-500'],
        'blue' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-500'],
        'amber' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-500'],
        'red' => ['bg' => 'bg-red-50', 'text' => 'text-red-500'],
    ];
    $colors = $colorMap[$color] ?? $colorMap['accent'];
@endphp

<div class="dashboard-card group">
    <div class="flex items-start justify-between">
        {{-- Icon --}}
        <div class="w-12 h-12 {{ $colors['bg'] }} rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
            <svg class="w-6 h-6 {{ $colors['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $icon }}"/>
            </svg>
        </div>

        {{-- Trend indicator (optional) --}}
        @if($trend)
            <span class="inline-flex items-center gap-1 text-xs font-medium {{ $trendUp ? 'text-emerald-600' : 'text-red-500' }}">
                @if($trendUp)
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                    </svg>
                @else
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                    </svg>
                @endif
                {{ $trend }}
            </span>
        @endif
    </div>

    {{-- Stat value and label --}}
    <div class="mt-4">
        <p class="text-2xl font-bold text-coffee-800">{{ $value }}</p>
        <p class="text-sm text-coffee-400 mt-1">{{ $title }}</p>
    </div>
</div>
