{{-- Dedicated Customer Cafe Discovery Page --}}
<x-dashboard-layout dashboard-role="customer">
    <x-slot name="title">Explore Cafes</x-slot>

    <div class="space-y-8" x-data="{
        filtersOpen: false,
        selectedCategory: '{{ $category ?? 'all' }}',
        activeFilterCount: 0
    }">

        {{-- Page Header --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 pb-2 border-b border-cream-200/80">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cream-200/60 border border-cream-300/60 text-xs font-semibold text-coffee-700 tracking-wide uppercase mb-2">
                    <span>Discovery & Booking</span>
                </div>
                <h1 class="font-serif text-3xl sm:text-4xl font-bold text-coffee-950 tracking-tight">Explore Cafes</h1>
                <p class="font-serif italic text-base sm:text-lg text-coffee-600 mt-1">Find a place that feels like yours.</p>
            </div>

            {{-- Filter toggle button for mobile/tablet --}}
            <div class="flex items-center gap-3">
                <span class="text-xs text-coffee-500 font-medium">Showing <strong class="text-coffee-900">{{ count($cafes) }}</strong> spaces</span>
                <button @click="filtersOpen = !filtersOpen"
                        class="inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold rounded-xl bg-white border border-cream-300 hover:border-coffee-400 text-coffee-800 transition-all shadow-xs">
                    <svg class="w-4 h-4 text-coffee-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    <span>Filters</span>
                    <span class="w-2 h-2 rounded-full bg-accent-500"></span>
                </button>
            </div>
        </div>

        {{-- Search & Primary Category Quick Pills (Panel 2 in reference) --}}
        <div class="space-y-4">
            <form action="{{ route('customer.explore') }}" method="GET" class="glass-card rounded-2xl p-2 sm:p-2.5 border border-cream-200 shadow-card">
                <div class="flex items-center gap-2">
                    <div class="relative flex-1">
                        <svg class="w-5 h-5 text-coffee-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text"
                               name="q"
                               value="{{ $query ?? '' }}"
                               placeholder="Search cafes, locations or cuisine..."
                               class="w-full pl-10 pr-4 py-2.5 bg-white/70 hover:bg-white focus:bg-white text-sm text-coffee-900 placeholder-coffee-400 rounded-xl border border-cream-200/80 focus:border-accent-400 focus:ring-2 focus:ring-accent-400/20 transition-all">
                    </div>
                    <button type="submit" class="btn-primary py-2.5 px-6 text-sm font-semibold tracking-tight shrink-0">
                        Search
                    </button>
                </div>
            </form>

            {{-- Category Filter Pills --}}
            <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none text-xs">
                <a href="{{ route('customer.explore') }}"
                   class="px-4 py-2 rounded-full font-medium transition-all shrink-0 {{ ($category ?? 'all') === 'all' ? 'bg-coffee-900 text-cream-50 font-semibold shadow-xs' : 'bg-white/80 hover:bg-white text-coffee-700 border border-cream-200' }}">
                    All
                </a>
                <a href="{{ route('customer.explore', ['category' => 'specialty-coffee']) }}"
                   class="px-4 py-2 rounded-full font-medium transition-all shrink-0 {{ ($category ?? '') === 'specialty-coffee' ? 'bg-coffee-900 text-cream-50 font-semibold shadow-xs' : 'bg-white/80 hover:bg-white text-coffee-700 border border-cream-200' }}">
                    Specialty Coffee
                </a>
                <a href="{{ route('customer.explore', ['category' => 'brunch']) }}"
                   class="px-4 py-2 rounded-full font-medium transition-all shrink-0 {{ ($category ?? '') === 'brunch' ? 'bg-coffee-900 text-cream-50 font-semibold shadow-xs' : 'bg-white/80 hover:bg-white text-coffee-700 border border-cream-200' }}">
                    Brunch
                </a>
                <a href="{{ route('customer.explore', ['category' => 'desserts']) }}"
                   class="px-4 py-2 rounded-full font-medium transition-all shrink-0 {{ ($category ?? '') === 'desserts' ? 'bg-coffee-900 text-cream-50 font-semibold shadow-xs' : 'bg-white/80 hover:bg-white text-coffee-700 border border-cream-200' }}">
                    Desserts
                </a>
                <a href="{{ route('customer.explore', ['category' => 'work-friendly']) }}"
                   class="px-4 py-2 rounded-full font-medium transition-all shrink-0 {{ ($category ?? '') === 'work-friendly' ? 'bg-coffee-900 text-cream-50 font-semibold shadow-xs' : 'bg-white/80 hover:bg-white text-coffee-700 border border-cream-200' }}">
                    Remote Work
                </a>
                <a href="{{ route('customer.explore', ['category' => 'pet-friendly']) }}"
                   class="px-4 py-2 rounded-full font-medium transition-all shrink-0 {{ ($category ?? '') === 'pet-friendly' ? 'bg-coffee-900 text-cream-50 font-semibold shadow-xs' : 'bg-white/80 hover:bg-white text-coffee-700 border border-cream-200' }}">
                    Pet Friendly
                </a>
            </div>
        </div>

        {{-- Collapsible Comprehensive Filter Drawer / Panel --}}
        <div x-show="filtersOpen"
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="glass-card-warm rounded-3xl p-6 border border-cream-300 shadow-card">
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-cream-200">
                <h3 class="font-serif text-lg font-bold text-coffee-900">Refine Cafe Search</h3>
                <a href="{{ route('customer.explore') }}" class="text-xs font-semibold text-coffee-500 hover:text-coffee-900 underline">Reset all</a>
            </div>

            <form action="{{ route('customer.explore') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
                {{-- Location --}}
                <div>
                    <label class="block font-medium text-coffee-700 mb-1.5">Location</label>
                    <select name="location" class="w-full bg-white text-xs text-coffee-800 rounded-xl border border-cream-200 py-2 px-3">
                        <option value="all">All Locations</option>
                        <option value="colombo-07">Colombo 07</option>
                        <option value="colombo-03">Colombo 03</option>
                        <option value="colombo-05">Colombo 05</option>
                        <option value="kandy">Kandy</option>
                        <option value="galle">Galle</option>
                        <option value="nugegoda">Nugegoda</option>
                    </select>
                </div>

                {{-- Date --}}
                <div>
                    <label class="block font-medium text-coffee-700 mb-1.5">Date</label>
                    <input type="date" value="2026-10-04" class="w-full bg-white text-xs text-coffee-800 rounded-xl border border-cream-200 py-2 px-3">
                </div>

                {{-- Time --}}
                <div>
                    <label class="block font-medium text-coffee-700 mb-1.5">Preferred Time</label>
                    <select class="w-full bg-white text-xs text-coffee-800 rounded-xl border border-cream-200 py-2 px-3">
                        <option>Any Time</option>
                        <option>Morning (8:00 AM - 12:00 PM)</option>
                        <option>Afternoon (12:00 PM - 4:00 PM)</option>
                        <option>Evening (4:00 PM - 8:00 PM)</option>
                        <option>Night (8:00 PM onwards)</option>
                    </select>
                </div>

                {{-- Guests --}}
                <div>
                    <label class="block font-medium text-coffee-700 mb-1.5">Guests</label>
                    <select class="w-full bg-white text-xs text-coffee-800 rounded-xl border border-cream-200 py-2 px-3">
                        <option>2 Guests</option>
                        <option>1 Guest (Solo / Work)</option>
                        <option>3 - 4 Guests</option>
                        <option>5+ Guests</option>
                    </select>
                </div>

                {{-- Rating --}}
                <div>
                    <label class="block font-medium text-coffee-700 mb-1.5">Minimum Rating</label>
                    <select class="w-full bg-white text-xs text-coffee-800 rounded-xl border border-cream-200 py-2 px-3">
                        <option>Any Rating</option>
                        <option>★ 4.5 & above</option>
                        <option>★ 4.8 & above (Top Rated)</option>
                    </select>
                </div>

                {{-- Cafe Type --}}
                <div>
                    <label class="block font-medium text-coffee-700 mb-1.5">Cafe Type</label>
                    <select name="type" class="w-full bg-white text-xs text-coffee-800 rounded-xl border border-cream-200 py-2 px-3">
                        <option value="all">All Types</option>
                        <option value="specialty">Specialty Roastery</option>
                        <option value="brunch">Brunch & Bistro</option>
                        <option value="desserts">Bakery & Patisserie</option>
                    </select>
                </div>

                {{-- Availability --}}
                <div>
                    <label class="block font-medium text-coffee-700 mb-1.5">Availability</label>
                    <select class="w-full bg-white text-xs text-coffee-800 rounded-xl border border-cream-200 py-2 px-3">
                        <option>Any Availability</option>
                        <option>Available Today</option>
                        <option>Instant Confirmation Only</option>
                    </select>
                </div>

                {{-- Reservation Fee / Price --}}
                <div class="flex items-end">
                    <button type="submit" class="btn-primary w-full py-2 text-xs font-semibold">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>

        {{-- Cafe Grid: 3 columns Desktop, 2 Tablet, 1 Mobile (as requested) --}}
        @if(count($cafes) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($cafes as $cafe)
                    <x-cafe-card :cafe="$cafe" />
                @endforeach
            </div>
        @else
            <div class="text-center py-16 bg-white rounded-3xl border border-cream-200 p-8">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-cream-100 flex items-center justify-center text-coffee-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <h3 class="font-serif text-xl font-bold text-coffee-900">No cafes found</h3>
                <p class="text-xs text-coffee-500 mt-1 max-w-sm mx-auto">We couldn't find any cafes matching your search criteria. Try removing some filters or exploring all cafes.</p>
                <div class="mt-5">
                    <a href="{{ route('customer.explore') }}" class="btn-secondary text-xs">Clear all filters</a>
                </div>
            </div>
        @endif

    </div>
</x-dashboard-layout>
