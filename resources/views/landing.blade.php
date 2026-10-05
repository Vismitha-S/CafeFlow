<x-public-layout>
    <x-slot name="title">CafeFlow - Discover & Reserve the Best Cafes</x-slot>

    {{-- ==========================================
         SECTION 1: Hero
         Main landing hero with headline, CTAs, and imagery
         ========================================== --}}
    <section class="relative pt-24 lg:pt-32 pb-16 lg:pb-24 overflow-hidden">
        {{-- Decorative background gradient --}}
        <div class="absolute inset-0 bg-gradient-to-br from-cream-100 via-cream-50 to-accent-50/30 -z-10"></div>

        {{-- Decorative coffee bean shapes --}}
        <div class="absolute top-20 right-10 w-24 h-24 bg-accent-100 rounded-full opacity-40 blur-2xl"></div>
        <div class="absolute bottom-10 left-10 w-32 h-32 bg-coffee-100 rounded-full opacity-30 blur-3xl"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">

                {{-- Hero text content --}}
                <div class="animate-fade-in-up">
                    <span class="badge-accent mb-4 text-sm">☕ Your Cafe Reservation Platform</span>
                    <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl font-bold text-coffee-900 leading-tight mb-6">
                        Discover & Reserve
                        <span class="text-accent-500 block">Your Perfect Cafe</span>
                    </h1>
                    <p class="text-lg text-coffee-500 leading-relaxed mb-8 max-w-lg">
                        Find amazing cafes near you, book your favourite table, and enjoy a seamless reservation experience. From cozy corners to vibrant spaces.
                    </p>

                    {{-- CTA buttons --}}
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="#featured-cafes" class="btn-primary text-base px-8 py-3.5">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Browse Cafes
                        </a>
                        <a href="{{ route('register') }}" class="btn-secondary text-base px-8 py-3.5">
                            Become a Cafe Owner
                        </a>
                    </div>

                    {{-- Trust indicators --}}
                    <div class="mt-10 flex items-center gap-8 text-sm text-coffee-400">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-accent-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Free to use</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-accent-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Secure deposits</span>
                        </div>
                        <div class="hidden sm:flex items-center gap-2">
                            <svg class="w-5 h-5 text-accent-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Instant booking</span>
                        </div>
                    </div>
                </div>

                {{-- Hero image --}}
                <div class="relative animate-fade-in lg:animate-slide-in-right">
                    <div class="relative rounded-3xl overflow-hidden shadow-2xl">
                        <img src="https://images.unsplash.com/photo-1554118811-1e0d58224f24?w=800&h=600&fit=crop&q=80"
                             alt="A beautifully lit cozy cafe interior with warm ambient lighting"
                             class="w-full h-[400px] lg:h-[500px] object-cover transition-transform duration-700 hover:scale-105"
                             loading="eager">
                        {{-- Gradient overlay at bottom --}}
                        <div class="absolute inset-0 bg-gradient-to-t from-coffee-900/20 to-transparent"></div>
                    </div>


                </div>
            </div>
        </div>
    </section>

    {{-- ==========================================
         SECTION 2: How CafeFlow Works
         Step-by-step process explanation
         ========================================== --}}
    <section id="how-it-works" class="py-20 lg:py-28 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Section header --}}
            <div class="text-center mb-16 scroll-reveal">
                <span class="badge-accent mb-3">Simple Process</span>
                <h2 class="section-heading mb-4">How CafeFlow Works</h2>
                <p class="text-coffee-400 text-lg max-w-2xl mx-auto">
                    Reserve your perfect cafe table in just a few simple steps.
                </p>
            </div>

            {{-- Steps grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-8">
                @php
                    // Mock data for the how-it-works steps
                    $steps = [
                        ['icon' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z', 'title' => 'Discover a Cafe', 'desc' => 'Browse cafes by location, cuisine, or ambiance.'],
                        ['icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'title' => 'Choose Date & Time', 'desc' => 'Pick your preferred date and time slot.'],
                        ['icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'title' => 'Reserve a Table', 'desc' => 'Select your table and confirm the booking.'],
                        ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'title' => 'Pay the Deposit', 'desc' => 'Secure your reservation with a small deposit.'],
                        ['icon' => 'M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'title' => 'Enjoy Your Visit', 'desc' => 'Arrive and enjoy — your table is waiting.'],
                    ];
                @endphp

                @foreach($steps as $index => $step)
                    <div class="scroll-reveal delay-{{ ($index + 1) * 100 }} text-center group">
                        {{-- Step number with icon --}}
                        <div class="relative inline-flex items-center justify-center w-16 h-16 bg-accent-50 rounded-2xl mb-5 group-hover:bg-accent-100 transition-colors duration-300">
                            <svg class="w-7 h-7 text-accent-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $step['icon'] }}"/>
                            </svg>
                            <span class="absolute -top-2 -right-2 w-6 h-6 bg-accent-500 text-white text-xs font-bold rounded-full flex items-center justify-center">{{ $index + 1 }}</span>
                        </div>
                        <h3 class="font-semibold text-coffee-800 text-base mb-2">{{ $step['title'] }}</h3>
                        <p class="text-sm text-coffee-400 leading-relaxed">{{ $step['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ==========================================
         SECTION 3: Featured Cafes
         Showcase cafe cards with mock data
         ========================================== --}}
    <section id="featured-cafes" class="py-20 lg:py-28 bg-cream-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Section header --}}
            <div class="text-center mb-16 scroll-reveal">
                <span class="badge-accent mb-3">Popular Picks</span>
                <h2 class="section-heading mb-4">Featured Cafes</h2>
                <p class="text-coffee-400 text-lg max-w-2xl mx-auto">
                    Explore some of the most loved cafes on CafeFlow.
                </p>
            </div>

            {{-- Cafe cards grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @php
                    // Mock cafe data for landing page display
                    $cafes = [
                        [
                            'name' => 'The Velvet Bean',
                            'location' => 'Colombo 07, Sri Lanka',
                            'desc' => 'A cozy artisan cafe with hand-crafted brews and freshly baked pastries in a warm, inviting atmosphere.',
                            'rating' => 4.8,
                            'reviews' => 124,
                            'image' => 'https://images.unsplash.com/photo-1559925393-8be0ec4767c8?w=600&h=400&fit=crop&q=80',
                        ],
                        [
                            'name' => 'Brew & Beyond',
                            'location' => 'Kandy, Sri Lanka',
                            'desc' => 'Modern specialty coffee bar with a minimalist aesthetic and panoramic views of the Kandy hills.',
                            'rating' => 4.6,
                            'reviews' => 89,
                            'image' => 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=600&h=400&fit=crop&q=80',
                        ],
                        [
                            'name' => 'Sunrise Roasters',
                            'location' => 'Galle, Sri Lanka',
                            'desc' => 'Beachside cafe serving single-origin pour-overs alongside ocean breezes and stunning sunsets.',
                            'rating' => 4.9,
                            'reviews' => 203,
                            'image' => 'https://images.unsplash.com/photo-1445116572660-236099ec97a0?w=600&h=400&fit=crop&q=80',
                        ],
                    ];
                @endphp

                @foreach($cafes as $index => $cafe)
                    <article class="cafe-card scroll-reveal delay-{{ ($index + 1) * 100 }}" role="article">
                        {{-- Cafe image --}}
                        <div class="relative overflow-hidden h-56">
                            <img src="{{ $cafe['image'] }}"
                                 alt="{{ $cafe['name'] }} cafe interior"
                                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                                 loading="lazy">
                            {{-- Rating badge overlay --}}
                            <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm rounded-lg px-2.5 py-1 flex items-center gap-1 shadow-sm">
                                <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <span class="text-sm font-semibold text-coffee-800">{{ $cafe['rating'] }}</span>
                            </div>
                        </div>

                        {{-- Cafe details --}}
                        <div class="p-5">
                            <h3 class="font-semibold text-lg text-coffee-800 mb-1">{{ $cafe['name'] }}</h3>
                            <div class="flex items-center gap-1.5 text-sm text-coffee-400 mb-3">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ $cafe['location'] }}
                            </div>
                            <p class="text-sm text-coffee-400 leading-relaxed mb-4">{{ $cafe['desc'] }}</p>

                            <div class="flex items-center justify-between">
                                <span class="text-xs text-coffee-300">{{ $cafe['reviews'] }} reviews</span>
                                <a href="#" class="inline-flex items-center text-sm font-semibold text-accent-500 hover:text-accent-600 transition-colors duration-200">
                                    View Cafe
                                    <svg class="w-4 h-4 ml-1 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ==========================================
         SECTION 4: Reservation Benefits
         Why customers should use CafeFlow
         ========================================== --}}
    <section class="py-20 lg:py-28 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16 items-center">

                {{-- Benefits image --}}
                <div class="scroll-reveal relative rounded-3xl overflow-hidden shadow-xl">
                    <img src="https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=700&h=500&fit=crop&q=80"
                         alt="Customer enjoying a latte in a cozy cafe setting"
                         class="w-full h-[400px] object-cover transition-transform duration-700 hover:scale-105"
                         loading="lazy">
                </div>

                {{-- Benefits list --}}
                <div class="scroll-reveal delay-200">
                    <span class="badge-accent mb-3">Why CafeFlow</span>
                    <h2 class="section-heading mb-6">Reservation Benefits</h2>
                    <p class="text-coffee-400 text-lg mb-8 leading-relaxed">
                        CafeFlow takes the hassle out of cafe reservations, giving you a smooth experience from discovery to your first sip.
                    </p>

                    @php
                        // Benefits with icons and descriptions
                        $benefits = [
                            ['icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'title' => 'Easy Reservations', 'desc' => 'Book a table in seconds with our intuitive reservation system.'],
                            ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'title' => 'Deposit Protection', 'desc' => 'Your deposit is safe and refundable according to each cafe\'s policy.'],
                            ['icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10', 'title' => 'Table Availability', 'desc' => 'See real-time table availability before you commit.'],
                            ['icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01', 'title' => 'Pre-order Food', 'desc' => 'Browse the menu and pre-order so your food is ready when you arrive.'],
                            ['icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'title' => 'Calendar Integration', 'desc' => 'Sync reservations with your personal calendar automatically.'],
                        ];
                    @endphp

                    <div class="space-y-5">
                        @foreach($benefits as $benefit)
                            <div class="flex items-start gap-4 group">
                                <div class="w-10 h-10 bg-accent-50 rounded-xl flex items-center justify-center shrink-0 group-hover:bg-accent-100 transition-colors duration-300">
                                    <svg class="w-5 h-5 text-accent-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $benefit['icon'] }}"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-coffee-800 mb-1">{{ $benefit['title'] }}</h3>
                                    <p class="text-sm text-coffee-400 leading-relaxed">{{ $benefit['desc'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ==========================================
         SECTION 5: For Cafe Owners
         Explain owner features and management tools
         ========================================== --}}
    <section id="for-owners" class="py-20 lg:py-28 bg-gradient-to-br from-coffee-800 to-coffee-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Section header --}}
            <div class="text-center mb-16 scroll-reveal">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-accent-500/20 text-accent-300 mb-3">For Business</span>
                <h2 class="font-display text-3xl md:text-4xl font-bold text-white tracking-tight mb-4">For Cafe Owners</h2>
                <p class="text-cream-300 text-lg max-w-2xl mx-auto">
                    Manage your cafe effortlessly with CafeFlow's powerful owner dashboard. Everything you need in one place.
                </p>
            </div>

            {{-- Owner features grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @php
                    // Owner feature cards with descriptions
                    $ownerFeatures = [
                        ['icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'title' => 'Table Management', 'desc' => 'Manage your tables, seating capacity, and floor plan layout.'],
                        ['icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'title' => 'Menu Builder', 'desc' => 'Create and update your digital menu with categories, prices, and images.'],
                        ['icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'title' => 'Reservation Control', 'desc' => 'Accept, manage, and organise reservations with real-time updates.'],
                        ['icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'title' => 'Customer Activity', 'desc' => 'Track customer visits, preferences, and feedback in one place.'],
                        ['icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'title' => 'Analytics Dashboard', 'desc' => 'View revenue trends, peak hours, and customer insights at a glance.'],
                        ['icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z', 'title' => 'Cafe Settings', 'desc' => 'Customise operating hours, deposit policies, and cafe details.'],
                    ];
                @endphp

                @foreach($ownerFeatures as $index => $feature)
                    <div class="scroll-reveal delay-{{ ($index + 1) * 100 }} bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/10 hover:bg-white/10 transition-all duration-300">
                        <div class="w-12 h-12 bg-accent-500/20 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-accent-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $feature['icon'] }}"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-white mb-2">{{ $feature['title'] }}</h3>
                        <p class="text-sm text-cream-300 leading-relaxed">{{ $feature['desc'] }}</p>
                    </div>
                @endforeach
            </div>

            {{-- Owner CTA --}}
            <div class="text-center mt-12 scroll-reveal">
                <a href="{{ route('register') }}" class="btn-primary text-base px-10 py-4">
                    Register Your Cafe
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>
        </div>
    </section>

    {{-- ==========================================
         SECTION 6: Testimonials
         Customer feedback section with mock reviews
         ========================================== --}}
    <section class="py-20 lg:py-28 bg-cream-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Section header --}}
            <div class="text-center mb-16 scroll-reveal">
                <span class="badge-accent mb-3">Testimonials</span>
                <h2 class="section-heading mb-4">What Our Customers Say</h2>
                <p class="text-coffee-400 text-lg max-w-2xl mx-auto">
                    Real experiences from CafeFlow users who love discovering and booking cafes.
                </p>
            </div>

            {{-- Testimonial cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @php
                    // Mock testimonial data
                    $testimonials = [
                        [
                            'name' => 'Amara Perera',
                            'role' => 'Coffee Enthusiast',
                            'quote' => 'CafeFlow made finding new cafes so enjoyable! I love being able to see real-time table availability before heading out. The deposit system gives me peace of mind.',
                            'rating' => 5,
                        ],
                        [
                            'name' => 'Dinesh Fernando',
                            'role' => 'Cafe Owner',
                            'quote' => 'Since joining CafeFlow, our reservations have increased by 40%. The owner dashboard makes it incredibly easy to manage tables and track customer activity.',
                            'rating' => 5,
                        ],
                        [
                            'name' => 'Nishara Silva',
                            'role' => 'Regular Customer',
                            'quote' => 'I use CafeFlow every weekend. The pre-ordering feature is a game-changer — my latte is ready by the time I sit down. Absolutely love it!',
                            'rating' => 5,
                        ],
                    ];
                @endphp

                @foreach($testimonials as $index => $testimonial)
                    <div class="scroll-reveal delay-{{ ($index + 1) * 100 }} bg-white rounded-2xl p-6 shadow-sm border border-cream-100 hover:shadow-md transition-shadow duration-300">
                        {{-- Star rating --}}
                        <div class="flex gap-1 mb-4">
                            @for($i = 0; $i < $testimonial['rating']; $i++)
                                <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>

                        {{-- Quote --}}
                        <blockquote class="text-coffee-500 text-sm leading-relaxed mb-6">
                            "{{ $testimonial['quote'] }}"
                        </blockquote>

                        {{-- Author --}}
                        <div class="flex items-center gap-3">
                            // Avatar initial circle
                            <div class="w-10 h-10 bg-accent-100 rounded-full flex items-center justify-center">
                                <span class="text-accent-600 font-semibold text-sm">{{ substr($testimonial['name'], 0, 1) }}</span>
                            </div>
                            <div>
                                <p class="font-semibold text-coffee-800 text-sm">{{ $testimonial['name'] }}</p>
                                <p class="text-xs text-coffee-400">{{ $testimonial['role'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ==========================================
         SECTION 7: Call to Action
         Final CTA section encouraging user signup
         ========================================== --}}
    <section class="py-20 lg:py-28 bg-gradient-to-br from-accent-500 to-accent-600 relative overflow-hidden">
        {{-- Decorative circles --}}
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10 scroll-reveal">
            <h2 class="font-display text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-6">
                Ready to Find Your Next Cafe?
            </h2>
            <p class="text-white/80 text-lg mb-10 max-w-2xl mx-auto">
                Join thousands of cafe lovers who use CafeFlow to discover, reserve, and enjoy amazing cafe experiences every day.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="#featured-cafes" class="inline-flex items-center justify-center px-8 py-3.5 bg-white text-accent-600 font-semibold rounded-xl hover:bg-cream-50 transition-all duration-300 transform hover:scale-[1.02]">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Browse Cafes
                </a>
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-3.5 bg-transparent border-2 border-white text-white font-semibold rounded-xl hover:bg-white hover:text-accent-600 transition-all duration-300">
                    Get Started Free
                </a>
            </div>
        </div>
    </section>
</x-public-layout>
