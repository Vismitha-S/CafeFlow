{{-- Reusable Cafe card component matching CafeFlow vintage design --}}
@props(['cafe', 'compact' => false])

@php
    $slug = $cafe['slug'] ?? 'the-velvet-bean';
    $name = $cafe['name'] ?? 'The Velvet Bean';
    $location = $cafe['location'] ?? 'Colombo 07';
    $rating = $cafe['rating'] ?? 4.8;
    $reviewsCount = $cafe['reviews_count'] ?? 320;
    $image = $cafe['image'] ?? 'https://images.unsplash.com/photo-1554118811-1e0d58224f24?auto=format&fit=crop&w=800&q=80';
    $cafeType = $cafe['cafe_type'] ?? 'Specialty Coffee';
    $tags = $cafe['tags'] ?? ['Specialty Coffee', 'Brunch'];
    $shortDesc = $cafe['short_description'] ?? 'Artisanal roastery & tranquil sanctuary.';
    $availability = $cafe['availability_status'] ?? 'Available today';
    $isFav = $cafe['is_favourite'] ?? false;
    $distance = $cafe['distance'] ?? null;
@endphp

<div x-data="{ isFavourite: {{ $isFav ? 'true' : 'false' }}, animateHeart: false }"
     class="cafe-card group flex flex-col h-full bg-white rounded-3xl border border-cream-200/90 shadow-subtle hover:shadow-card-hover transition-all duration-300">

    {{-- Card Cover Image with heart button & availability badge --}}
    <div class="relative aspect-4/3 w-full overflow-hidden bg-cream-100 rounded-t-3xl">
        <img src="{{ $image }}"
             alt="{{ $name }}"
             class="w-full h-full object-cover transition-transform duration-500 ease-out group-hover:scale-105"
             loading="lazy">

        {{-- Subtle gradient overlay --}}
        <div class="absolute inset-0 bg-gradient-to-t from-coffee-950/60 via-transparent to-black/20"></div>

        {{-- Top badges --}}
        <div class="absolute top-3.5 inset-x-3.5 flex items-center justify-between z-10">
            {{-- Availability pill --}}
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-medium backdrop-blur-md bg-white/85 text-coffee-800 border border-white/40 shadow-xs">
                <span class="w-2 h-2 rounded-full {{ str_contains(strtolower($availability), 'almost') ? 'bg-amber-500' : 'bg-sage-500 animate-pulse' }}"></span>
                <span>{{ $availability }}</span>
            </span>

            {{-- Interactive Favourite heart button --}}
            <button @click.prevent.stop="isFavourite = !isFavourite; animateHeart = true; setTimeout(() => animateHeart = false, 300)"
                    class="w-9 h-9 rounded-full flex items-center justify-center backdrop-blur-md bg-white/85 hover:bg-white text-coffee-700 shadow-xs transition-transform duration-200 active:scale-90"
                    :class="{ 'scale-115 text-rose-500': animateHeart }"
                    aria-label="Add to favourites">
                <svg class="w-4 h-4 transition-colors duration-200"
                     :class="isFavourite ? 'fill-rose-500 text-rose-500' : 'text-coffee-600 fill-none'"
                     stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </button>
        </div>

        {{-- Bottom image info: rating & distance --}}
        <div class="absolute bottom-3 inset-x-3.5 flex items-center justify-between text-white text-xs z-10">
            <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg backdrop-blur-md bg-coffee-950/40 border border-white/10 font-medium">
                <svg class="w-3.5 h-3.5 text-brass-400 fill-brass-400" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                <span class="font-semibold">{{ number_format($rating, 1) }}</span>
                <span class="text-white/70 text-[11px]">({{ $reviewsCount }})</span>
            </div>

            @if($distance)
                <span class="px-2.5 py-1 rounded-lg backdrop-blur-md bg-coffee-950/40 border border-white/10 font-medium text-[11px] text-cream-100">
                    {{ $distance }}
                </span>
            @endif
        </div>
    </div>

    {{-- Card Body --}}
    <div class="p-5 flex flex-col flex-1 justify-between">
        <div>
            {{-- Cafe Name & Location --}}
            <div class="flex items-start justify-between gap-2 mb-1.5">
                <a href="{{ route('customer.cafe.show', $slug) }}" class="group-hover:text-accent-600 transition-colors duration-200">
                    <h3 class="font-serif text-lg font-bold text-coffee-900 tracking-tight leading-snug">{{ $name }}</h3>
                </a>
            </div>

            <p class="flex items-center gap-1.5 text-xs text-coffee-500 mb-3">
                <svg class="w-3.5 h-3.5 text-accent-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>{{ $location }}</span>
                <span class="text-coffee-300">•</span>
                <span class="font-medium text-coffee-600">{{ $cafeType }}</span>
            </p>

            @if(!$compact)
                <p class="text-xs text-coffee-500 line-clamp-2 leading-relaxed mb-4">
                    {{ $shortDesc }}
                </p>
            @endif

            {{-- Tags --}}
            <div class="flex flex-wrap gap-1.5 mb-4">
                @foreach(array_slice($tags, 0, 3) as $tag)
                    <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-medium bg-cream-100 text-coffee-600 border border-cream-200/80">
                        {{ $tag }}
                    </span>
                @endforeach
            </div>
        </div>

        {{-- Card footer action --}}
        <div class="pt-3 border-t border-cream-100 flex items-center justify-between mt-auto">
            <span class="text-[11px] text-coffee-400 font-medium">Instant Booking</span>
            <a href="{{ route('customer.cafe.show', $slug) }}"
               class="inline-flex items-center gap-1 px-3.5 py-1.5 text-xs font-semibold text-accent-600 hover:text-white bg-accent-50 hover:bg-accent-500 rounded-xl border border-accent-200/60 hover:border-accent-500 transition-all duration-200">
                <span>View Cafe</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </div>
</div>
