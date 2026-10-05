{{-- Customer Personalized Discovery Home --}}
<x-dashboard-layout dashboard-role="customer">
    <x-slot name="title">Home</x-slot>

    <div class="space-y-12">

        {{-- 1. Personalized Greeting Section with subtle coffee steam & floral motif --}}
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-cream-100/90 via-white/80 to-cream-100/60 border border-cream-200/90 p-6 sm:p-10 shadow-subtle">
        {{-- Decorative background: hand-drawn style coffee illustration --}}
        <div class="absolute right-0 bottom-0 top-0 w-80 pointer-events-none select-none overflow-hidden hidden lg:block">
            <svg viewBox="0 0 340 300" fill="none" xmlns="http://www.w3.org/2000/svg" class="absolute right-0 bottom-0 h-full w-auto opacity-[0.18]" stroke="#5C3317" stroke-linecap="round" stroke-linejoin="round">
                {{-- Steam wisps --}}
                <path d="M180 60 Q175 45 180 30 Q185 15 180 0" stroke-width="2.5" fill="none"/>
                <path d="M196 65 Q191 50 196 35 Q201 20 196 5" stroke-width="2.5" fill="none"/>
                <path d="M212 62 Q207 47 212 32 Q217 17 212 2" stroke-width="2.5" fill="none"/>

                {{-- Moka pot body --}}
                <path d="M160 200 L165 130 Q166 120 175 118 L215 118 Q224 120 225 130 L230 200 Z" stroke-width="2.8" fill="none"/>
                {{-- Moka pot top chamber --}}
                <path d="M170 118 Q172 95 185 88 L205 88 Q218 95 220 118" stroke-width="2.5" fill="none"/>
                {{-- Moka pot lid / cap --}}
                <ellipse cx="195" cy="86" rx="14" ry="6" stroke-width="2.5" fill="none"/>
                <rect x="190" y="76" width="10" height="11" rx="3" stroke-width="2.2" fill="none"/>
                {{-- Moka pot handle --}}
                <path d="M230 155 Q258 155 260 170 Q262 185 242 188 L230 188" stroke-width="2.8" fill="none"/>
                {{-- Moka pot base ring --}}
                <ellipse cx="195" cy="200" rx="35" ry="6" stroke-width="2" fill="none"/>
                {{-- Waist of moka pot --}}
                <path d="M165 160 Q195 168 225 160" stroke-width="1.8" fill="none"/>

                {{-- Pouring stream --}}
                <path d="M163 175 Q140 185 120 195 Q100 210 95 225" stroke-width="3" fill="none"/>
                <path d="M163 178 Q138 190 118 202 Q98 218 97 230" stroke-width="2" fill="none"/>

                {{-- Coffee cup --}}
                <path d="M60 225 L75 285 Q76 292 85 292 L125 292 Q134 292 135 285 L150 225 Z" stroke-width="2.8" fill="none"/>
                {{-- Cup handle --}}
                <path d="M150 240 Q172 240 172 257 Q172 274 150 274" stroke-width="2.5" fill="none"/>
                {{-- Cup saucer --}}
                <ellipse cx="105" cy="293" rx="52" ry="8" stroke-width="2" fill="none"/>
                {{-- Coffee liquid in cup --}}
                <path d="M68 243 Q105 252 142 243" stroke-width="1.8" fill="none"/>

                {{-- Coffee beans scattered --}}
                <ellipse cx="270" cy="100" rx="13" ry="9" stroke-width="2.2" fill="none" transform="rotate(-25 270 100)"/>
                <path d="M263 100 Q270 93 277 100" stroke-width="1.5" fill="none" transform="rotate(-25 270 100)"/>

                <ellipse cx="295" cy="240" rx="13" ry="9" stroke-width="2.2" fill="none" transform="rotate(15 295 240)"/>
                <path d="M288 240 Q295 233 302 240" stroke-width="1.5" fill="none" transform="rotate(15 295 240)"/>

                <ellipse cx="60" cy="180" rx="11" ry="7" stroke-width="2" fill="none" transform="rotate(40 60 180)"/>
                <path d="M54 180 Q60 174 66 180" stroke-width="1.4" fill="none" transform="rotate(40 60 180)"/>

                <ellipse cx="300" cy="170" rx="10" ry="7" stroke-width="2" fill="none" transform="rotate(-10 300 170)"/>
                <path d="M294 170 Q300 164 306 170" stroke-width="1.4" fill="none" transform="rotate(-10 300 170)"/>

                {{-- Leaf sprigs --}}
                <path d="M50 130 Q60 110 80 118 Q65 130 50 130Z" stroke-width="2" fill="none"/>
                <path d="M50 130 Q55 120 65 124" stroke-width="1.4" fill="none"/>
                <path d="M48 130 Q38 115 55 108 Q50 122 48 130Z" stroke-width="2" fill="none"/>

                <path d="M285 60 Q295 42 315 50 Q300 62 285 60Z" stroke-width="2" fill="none"/>
                <path d="M285 60 Q290 50 302 54" stroke-width="1.4" fill="none"/>
                <path d="M283 60 Q273 45 290 38 Q285 52 283 60Z" stroke-width="2" fill="none"/>

                {{-- Splash drops from pour --}}
                <circle cx="90" cy="218" r="3" stroke-width="1.8" fill="none"/>
                <circle cx="82" cy="226" r="2" stroke-width="1.6" fill="none"/>
                <circle cx="100" cy="222" r="2.5" stroke-width="1.6" fill="none"/>

                {{-- Small dots / texture --}}
                <circle cx="250" cy="135" r="2.5" stroke-width="1.5" fill="none"/>
                <circle cx="330" cy="200" r="3" stroke-width="1.5" fill="none"/>
                <circle cx="40" cy="260" r="2" stroke-width="1.5" fill="none"/>
            </svg>
        </div>

            <div class="relative z-10 max-w-3xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cream-200/60 border border-cream-300/60 text-xs font-semibold text-coffee-700 tracking-wide uppercase mb-3">
                    <span class="w-2 h-2 rounded-full bg-accent-500"></span>
                    <span>Artisanal Cafe Reservations</span>
                </div>

                <h1 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-bold text-coffee-950 tracking-tight leading-tight">
                    {{ $greeting ?? 'Good morning' }}, <span class="text-accent-600">{{ explode(' ', Auth::user()->name)[0] }}</span>
                </h1>

                <p class="font-serif italic text-lg sm:text-xl text-coffee-600 mt-2 font-normal">
                    Where are we having coffee today?
                </p>
            </div>

            {{-- 2. Elegant Search & Filter Bar --}}
            <div class="mt-8 relative z-10">
                <form action="{{ route('customer.explore') }}" method="GET" class="glass-card rounded-2xl p-2.5 sm:p-3 border border-cream-200 shadow-card">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-2.5 items-center">

                        {{-- Search Input --}}
                        <div class="md:col-span-5 relative flex items-center">
                            <svg class="w-5 h-5 text-coffee-400 absolute left-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text"
                                   name="q"
                                   placeholder="Search cafes, locations, cuisine..."
                                   class="w-full pl-10 pr-4 py-2.5 bg-white/70 hover:bg-white focus:bg-white text-sm text-coffee-900 placeholder-coffee-400 rounded-xl border border-cream-200/80 focus:border-accent-400 focus:ring-2 focus:ring-accent-400/20 transition-all">
                        </div>

                        {{-- Location Dropdown --}}
                        <div class="md:col-span-3 relative flex items-center">
                            <svg class="w-4 h-4 text-coffee-400 absolute left-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            </svg>
                            <select name="location"
                                    class="w-full pl-9 pr-8 py-2.5 bg-white/70 hover:bg-white focus:bg-white text-xs sm:text-sm text-coffee-800 rounded-xl border border-cream-200/80 focus:border-accent-400 focus:ring-2 focus:ring-accent-400/20 transition-all appearance-none cursor-pointer">
                                <option value="all">All Locations</option>
                                <option value="colombo-07">Colombo 07</option>
                                <option value="colombo-03">Colombo 03</option>
                                <option value="colombo-05">Colombo 05</option>
                                <option value="kandy">Kandy Hills</option>
                                <option value="galle">Galle Fort</option>
                                <option value="nugegoda">Nugegoda</option>
                            </select>
                        </div>

                        {{-- Guests Selector --}}
                        <div class="md:col-span-2 relative flex items-center">
                            <svg class="w-4 h-4 text-coffee-400 absolute left-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <select name="guests"
                                    class="w-full pl-9 pr-8 py-2.5 bg-white/70 hover:bg-white focus:bg-white text-xs sm:text-sm text-coffee-800 rounded-xl border border-cream-200/80 focus:border-accent-400 focus:ring-2 focus:ring-accent-400/20 transition-all appearance-none cursor-pointer">
                                <option value="2">2 Guests</option>
                                <option value="1">1 Guest</option>
                                <option value="3">3 Guests</option>
                                <option value="4">4 Guests</option>
                                <option value="6">5+ Guests</option>
                            </select>
                        </div>

                        {{-- Search CTA --}}
                        <div class="md:col-span-2">
                            <button type="submit" class="btn-primary w-full py-2.5 text-sm font-semibold tracking-tight shadow-sm">
                                <span>Find Cafes</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </form>

                {{-- Mood category pills --}}
                <div class="flex items-center gap-2 mt-4 overflow-x-auto pb-1 scrollbar-none text-xs">
                    <span class="text-coffee-500 font-medium whitespace-nowrap mr-1">Trending:</span>
                    <a href="{{ route('customer.explore') }}" class="px-3.5 py-1.5 rounded-full font-medium bg-coffee-900 text-cream-50 hover:bg-coffee-800 transition-colors shrink-0">
                        All
                    </a>
                    <a href="{{ route('customer.explore', ['category' => 'specialty-coffee']) }}" class="px-3.5 py-1.5 rounded-full font-medium bg-white/80 hover:bg-white text-coffee-700 border border-cream-200 hover:border-cream-300 transition-colors shrink-0">
                        Specialty Coffee
                    </a>
                    <a href="{{ route('customer.explore', ['category' => 'brunch']) }}" class="px-3.5 py-1.5 rounded-full font-medium bg-white/80 hover:bg-white text-coffee-700 border border-cream-200 hover:border-cream-300 transition-colors shrink-0">
                        Brunch
                    </a>
                    <a href="{{ route('customer.explore', ['category' => 'desserts']) }}" class="px-3.5 py-1.5 rounded-full font-medium bg-white/80 hover:bg-white text-coffee-700 border border-cream-200 hover:border-cream-300 transition-colors shrink-0">
                        Desserts
                    </a>
                    <a href="{{ route('customer.explore', ['category' => 'work-friendly']) }}" class="px-3.5 py-1.5 rounded-full font-medium bg-white/80 hover:bg-white text-coffee-700 border border-cream-200 hover:border-cream-300 transition-colors shrink-0">
                        Remote Work
                    </a>
                    <a href="{{ route('customer.explore', ['category' => 'pet-friendly']) }}" class="px-3.5 py-1.5 rounded-full font-medium bg-white/80 hover:bg-white text-coffee-700 border border-cream-200 hover:border-cream-300 transition-colors shrink-0">
                        Pet Friendly
                    </a>
                </div>
            </div>
        </div>

        {{-- 7. Elegant Quick Action Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <a href="{{ route('customer.explore') }}"
               class="group relative overflow-hidden rounded-3xl bg-white border border-cream-200 p-6 shadow-subtle hover:shadow-card-hover transition-all duration-300 hover:-translate-y-1">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-cream-100 flex items-center justify-center text-accent-600 group-hover:bg-accent-500 group-hover:text-white transition-colors duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <span class="text-xs font-semibold text-accent-600 flex items-center gap-1 group-hover:translate-x-1 transition-transform duration-200">
                        Explore
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </span>
                </div>
                <h3 class="font-serif text-lg font-bold text-coffee-900 group-hover:text-accent-600 transition-colors">Explore Cafes</h3>
                <p class="text-xs text-coffee-500 mt-1">Discover curated roasteries, tea salons & cozy nooks across Sri Lanka.</p>
            </a>

            <a href="{{ route('customer.reservations') }}"
               class="group relative overflow-hidden rounded-3xl bg-white border border-cream-200 p-6 shadow-subtle hover:shadow-card-hover transition-all duration-300 hover:-translate-y-1">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-sage-50 flex items-center justify-center text-sage-600 group-hover:bg-sage-600 group-hover:text-white transition-colors duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <span class="text-xs font-semibold text-sage-600 flex items-center gap-1 group-hover:translate-x-1 transition-transform duration-200">
                        View
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </span>
                </div>
                <h3 class="font-serif text-lg font-bold text-coffee-900 group-hover:text-sage-700 transition-colors">My Reservations</h3>
                <p class="text-xs text-coffee-500 mt-1">Check upcoming table bookings, arrival times and reservation details.</p>
            </a>

            <a href="{{ route('customer.favourites') }}"
               class="group relative overflow-hidden rounded-3xl bg-white border border-cream-200 p-6 shadow-subtle hover:shadow-card-hover transition-all duration-300 hover:-translate-y-1">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-rose-50 flex items-center justify-center text-rose-500 group-hover:bg-rose-500 group-hover:text-white transition-colors duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </div>
                    <span class="text-xs font-semibold text-rose-500 flex items-center gap-1 group-hover:translate-x-1 transition-transform duration-200">
                        Saved
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </span>
                </div>
                <h3 class="font-serif text-lg font-bold text-coffee-900 group-hover:text-rose-600 transition-colors">Favourites</h3>
                <p class="text-xs text-coffee-500 mt-1">Revisit your saved cafe gems, favorite corner tables, and wishlists.</p>
            </a>
        </div>

        {{-- Cafe Owner Callout Banner --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-coffee-900 via-coffee-800 to-coffee-950 p-5 sm:p-6 text-white shadow-card flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="space-y-1">
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-accent-500/20 text-accent-300 text-[11px] font-semibold tracking-wide uppercase">
                    <span>Cafe Partners</span>
                </div>
                <h3 class="font-serif text-lg sm:text-xl font-bold text-cream-50">Do you own or manage an artisanal cafe?</h3>
                <p class="text-xs sm:text-sm text-cream-200/80 max-w-xl">
                    Join CafeFlow to list your tables, configure reservation deposits, and receive guaranteed bookings.
                </p>
            </div>
            <form method="POST" action="{{ route('switch.to.owner') }}" class="shrink-0">
                @csrf
                <button type="submit" class="btn-primary !py-2.5 !px-5 text-xs font-semibold whitespace-nowrap shadow-md hover:scale-[1.02] transition-transform">
                    List Your Cafe &rarr;
                </button>
            </form>
        </div>

        {{-- 3. Recommended Cafes Section --}}
        <div>
            <div class="flex items-end justify-between mb-6">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-accent-600">Handpicked for you</span>
                    <h2 class="font-serif text-2xl sm:text-3xl font-bold text-coffee-900 tracking-tight mt-0.5">Recommended Cafes</h2>
                </div>
                <a href="{{ route('customer.explore') }}" class="text-xs sm:text-sm font-semibold text-accent-600 hover:text-accent-700 flex items-center gap-1">
                    <span>View all</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($recommendedCafes as $cafe)
                    <x-cafe-card :cafe="$cafe" />
                @endforeach
            </div>
        </div>

        {{-- 4. Nearby Cafes Discovery Section --}}
        <div class="rounded-3xl bg-cream-100/70 border border-cream-200/90 p-6 sm:p-8">
            <div class="flex items-end justify-between mb-6">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-coffee-500">Walking distance & quick drives</span>
                    <h2 class="font-serif text-2xl sm:text-3xl font-bold text-coffee-900 tracking-tight mt-0.5">Nearby Cafes</h2>
                </div>
                <span class="text-xs font-medium text-coffee-500 hidden sm:inline-flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-sage-500"></span>
                    Live Table Availability
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @foreach($nearbyCafes as $cafe)
                    <div class="bg-white rounded-2xl border border-cream-200 p-4 shadow-subtle hover:shadow-card hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between">
                        <div>
                            <div class="relative aspect-16/10 rounded-xl overflow-hidden mb-3">
                                <img src="{{ $cafe['image'] }}" alt="{{ $cafe['name'] }}" class="w-full h-full object-cover">
                                <span class="absolute top-2 left-2 px-2 py-0.5 rounded-md text-[10px] font-semibold backdrop-blur-md bg-white/90 text-coffee-800">
                                    {{ $cafe['distance'] }} away
                                </span>
                            </div>

                            <div class="flex items-center justify-between mb-1">
                                <h4 class="font-serif text-sm font-bold text-coffee-900 truncate">{{ $cafe['name'] }}</h4>
                                <div class="flex items-center gap-1 text-[11px] font-medium text-coffee-700">
                                    <svg class="w-3 h-3 text-brass-500 fill-brass-500" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    <span>{{ $cafe['rating'] }}</span>
                                </div>
                            </div>

                            <p class="text-[11px] text-coffee-500 mb-3">{{ $cafe['location'] }} • {{ $cafe['cafe_type'] }}</p>
                        </div>

                        <div class="pt-2.5 border-t border-cream-100 flex items-center justify-between">
                            <span class="text-[10px] font-medium text-sage-700 bg-sage-50 px-2 py-0.5 rounded-full border border-sage-200/60">
                                {{ $cafe['tables_left'] ?? 3 }} tables free
                            </span>
                            <a href="{{ route('customer.cafe.show', $cafe['slug']) }}" class="text-xs font-semibold text-accent-600 hover:text-accent-700">
                                Book &rarr;
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- 5. Popular Cafes Section & 6. Recently Viewed --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Popular Cafes --}}
            <div class="lg:col-span-2">
                <div class="flex items-end justify-between mb-5">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-accent-600">Crowd favorites</span>
                        <h2 class="font-serif text-2xl font-bold text-coffee-900 tracking-tight">Popular Cafes</h2>
                    </div>
                    <a href="{{ route('customer.explore') }}" class="text-xs font-semibold text-accent-600 hover:text-accent-700">Explore all</a>
                </div>

                <div class="space-y-4">
                    @foreach($popularCafes as $cafe)
                        <div class="glass-card rounded-2xl p-4 flex items-center justify-between gap-4 hover:border-cream-300 transition-all duration-200">
                            <div class="flex items-center gap-3.5 min-w-0">
                                <div class="w-16 h-16 rounded-xl overflow-hidden shrink-0 bg-cream-100">
                                    <img src="{{ $cafe['image'] }}" alt="{{ $cafe['name'] }}" class="w-full h-full object-cover">
                                </div>
                                <div class="min-w-0">
                                    <h4 class="font-serif font-bold text-coffee-900 text-sm sm:text-base truncate">{{ $cafe['name'] }}</h4>
                                    <p class="text-xs text-coffee-500 truncate">{{ $cafe['location'] }} • {{ $cafe['cuisine'] }}</p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <div class="flex items-center gap-1 text-xs font-semibold text-coffee-800">
                                            <svg class="w-3.5 h-3.5 text-brass-500 fill-brass-500" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            <span>{{ $cafe['rating'] }}</span>
                                            <span class="text-coffee-400 font-normal">({{ $cafe['reviews_count'] }})</span>
                                        </div>
                                        <span class="text-coffee-300">•</span>
                                        <span class="text-[11px] text-sage-700 font-medium">Instant Reserve</span>
                                    </div>
                                </div>
                            </div>

                            <a href="{{ route('customer.cafe.show', $cafe['slug']) }}" class="btn-secondary px-3.5 py-1.5 text-xs font-semibold shrink-0">
                                View
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Recently Viewed Cafes --}}
            <div>
                <div class="mb-5">
                    <span class="text-xs font-semibold uppercase tracking-wider text-coffee-400">Jump back in</span>
                    <h2 class="font-serif text-2xl font-bold text-coffee-900 tracking-tight">Recently Viewed</h2>
                </div>

                <div class="dashboard-card space-y-4">
                    @foreach($recentlyViewed as $recent)
                        <div class="flex items-center gap-3 pb-3 {{ !$loop->last ? 'border-b border-cream-100' : '' }}">
                            <div class="w-12 h-12 rounded-xl overflow-hidden shrink-0">
                                <img src="{{ $recent['image'] }}" alt="{{ $recent['name'] }}" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('customer.cafe.show', $recent['slug']) }}" class="font-serif text-sm font-bold text-coffee-900 hover:text-accent-600 truncate block">
                                    {{ $recent['name'] }}
                                </a>
                                <p class="text-[11px] text-coffee-400 truncate">{{ $recent['location'] }}</p>
                            </div>
                            <a href="{{ route('customer.cafe.show', $recent['slug']) }}" class="p-1.5 rounded-lg text-coffee-400 hover:text-accent-600 hover:bg-cream-100 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</x-dashboard-layout>
