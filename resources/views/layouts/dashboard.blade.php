{{-- CafeFlow dashboard layout with vintage cafe aesthetic --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'CafeFlow' }} - Artisanal Cafe Reservations</title>

        <!-- Google / Bunny Fonts for vintage cafe & editorial aesthetic -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=playfair-display:400,500,600,700,800|plus-jakarta-sans:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts and Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>
    <body class="h-full font-sans antialiased bg-[#FAF7F2] text-coffee-800 selection:bg-accent-100 selection:text-coffee-900">
        <x-banner />

        <div x-data="{ sidebarOpen: false, locationOpen: false }" class="min-h-screen flex bg-[#FAF7F2]">

            {{-- Sidebar navigation --}}
            @include('partials.dashboard-sidebar', ['dashboardRole' => $dashboardRole ?? 'customer'])

            {{-- Main content area --}}
            <div class="flex-1 flex flex-col min-w-0 pb-16 lg:pb-0">

                {{-- Top bar with subtle glassmorphism --}}
                <header class="sticky top-0 z-30 bg-[#FAF7F2]/85 backdrop-blur-md border-b border-cream-200/90 transition-all duration-200">
                    <div class="flex items-center justify-between h-20 px-4 sm:px-6 lg:px-8">
                        {{-- Mobile sidebar toggle & logo --}}
                        <div class="flex items-center gap-3">
                            <button @click="sidebarOpen = true"
                                    class="lg:hidden p-2 rounded-xl text-coffee-600 hover:bg-cream-100 transition-colors duration-200"
                                    aria-label="Open sidebar">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                            </button>
                            <a href="/" class="lg:hidden transition-transform duration-200 hover:scale-105" aria-label="CafeFlow Home">
                                <x-cafeflow-logo class="h-7 w-auto" />
                            </a>
                        </div>

                        {{-- Center / Left location selector pill (shown in reference topbar) --}}
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                    class="flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/70 border border-cream-200 hover:border-cream-300 hover:bg-white text-xs sm:text-sm font-medium text-coffee-800 shadow-xs transition-all duration-200">
                                <svg class="w-4 h-4 text-accent-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span>Colombo 07</span>
                                <svg class="w-3.5 h-3.5 text-coffee-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div x-show="open"
                                 @click.away="open = false"
                                 x-cloak
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute left-0 mt-2 w-48 bg-white rounded-2xl border border-cream-200 shadow-card py-2 z-50">
                                <div class="px-3 py-1.5 text-[11px] font-semibold tracking-wider uppercase text-coffee-400">Current Area</div>
                                <a href="#" @click="open = false" class="flex items-center justify-between px-3 py-2 text-xs text-coffee-800 hover:bg-cream-50 font-medium">
                                    <span>Colombo 07 (Cinnamon Gardens)</span>
                                    <span class="w-1.5 h-1.5 rounded-full bg-accent-500"></span>
                                </a>
                                <a href="#" @click="open = false" class="block px-3 py-2 text-xs text-coffee-600 hover:bg-cream-50 font-medium">
                                    Colombo 03 (Kollupitiya)
                                </a>
                                <a href="#" @click="open = false" class="block px-3 py-2 text-xs text-coffee-600 hover:bg-cream-50 font-medium">
                                    Galle Fort
                                </a>
                                <a href="#" @click="open = false" class="block px-3 py-2 text-xs text-coffee-600 hover:bg-cream-50 font-medium">
                                    Kandy Hills
                                </a>
                            </div>
                        </div>

                        {{-- Right items: notifications & user dropdown --}}
                        <div class="flex items-center gap-3 sm:gap-4">
                            {{-- Notification bell with subtle indicator --}}
                            <div class="relative" x-data="{ notifOpen: false }">
                                <button @click="notifOpen = !notifOpen"
                                        class="p-2.5 rounded-xl bg-white/70 border border-cream-200 text-coffee-600 hover:bg-white hover:text-coffee-900 transition-all duration-200 relative shadow-xs"
                                        aria-label="Notifications">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                    </svg>
                                    <span class="absolute top-2 right-2 w-2 h-2 bg-accent-500 rounded-full ring-2 ring-[#FAF7F2]"></span>
                                </button>

                                <div x-show="notifOpen"
                                     @click.away="notifOpen = false"
                                     x-cloak
                                     class="absolute right-0 mt-2 w-80 bg-white rounded-2xl border border-cream-200 shadow-card p-4 z-50">
                                    <div class="flex items-center justify-between pb-3 border-b border-cream-100">
                                        <h4 class="text-xs font-semibold uppercase tracking-wider text-coffee-700">Notifications</h4>
                                        <span class="badge-accent text-[10px]">1 New</span>
                                    </div>
                                    <div class="py-3 space-y-2.5">
                                        <div class="flex gap-3 items-start">
                                            <div class="w-2 h-2 mt-1.5 rounded-full bg-accent-500 shrink-0"></div>
                                            <div>
                                                <p class="text-xs font-medium text-coffee-800">Reservation Confirmed</p>
                                                <p class="text-[11px] text-coffee-500">The Velvet Bean is ready for you tomorrow at 10:30 AM.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- User dropdown --}}
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="flex items-center gap-2.5 p-1 sm:px-2.5 sm:py-1.5 rounded-full bg-white/70 border border-cream-200 hover:border-cream-300 hover:bg-white text-sm transition-all duration-200 shadow-xs">
                                        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos() && Auth::user()->profile_photo_path)
                                            <img class="w-8 h-8 rounded-full object-cover ring-1 ring-cream-300"
                                                 src="{{ Auth::user()->profile_photo_url }}"
                                                 alt="{{ Auth::user()->name }}">
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-cream-200 text-coffee-800 font-serif font-bold flex items-center justify-center text-xs ring-1 ring-cream-300">
                                                {{ strtoupper(substr(Auth::user()->name ?? 'V', 0, 1)) }}
                                            </div>
                                        @endif
                                        <span class="hidden md:block font-medium text-xs text-coffee-800">{{ Auth::user()->name }}</span>
                                        <svg class="w-3.5 h-3.5 text-coffee-400 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <div class="block px-4 py-2 text-[11px] font-semibold tracking-wider uppercase text-coffee-400">Personal Space</div>
                                    <x-dropdown-link href="{{ route('profile.show') }}">Profile & Account</x-dropdown-link>
                                    <x-dropdown-link href="{{ route('customer.reservations') }}">My Reservations</x-dropdown-link>
                                    <x-dropdown-link href="{{ route('customer.favourites') }}">Saved Cafes</x-dropdown-link>

                                    @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                        <x-dropdown-link href="{{ route('api-tokens.index') }}">API Tokens</x-dropdown-link>
                                    @endif

                                    <div class="border-t border-cream-100 my-1"></div>

                                    <form method="POST" action="{{ route('logout') }}" x-data>
                                        @csrf
                                        <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();" class="text-rose-600 hover:bg-rose-50">
                                            Log Out
                                        </x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    </div>
                </header>

                {{-- Main page content --}}
                <main class="flex-1 px-4 sm:px-6 lg:px-10 py-6 sm:py-8 max-w-7xl mx-auto w-full">
                    {{ $slot }}
                </main>
            </div>
        </div>

        {{-- Mobile bottom navigation bar for quick customer convenience --}}
        <nav class="lg:hidden fixed bottom-0 inset-x-0 bg-[#FAF7F2]/95 backdrop-blur-md border-t border-cream-200/90 z-40 px-3 py-2 flex items-center justify-around shadow-card">
            <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-1 text-[10px] font-medium {{ request()->routeIs('dashboard') || request()->routeIs('customer.dashboard') ? 'text-accent-600 font-semibold' : 'text-coffee-500' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span>Home</span>
            </a>
            <a href="{{ route('customer.explore') }}" class="flex flex-col items-center gap-1 text-[10px] font-medium {{ request()->routeIs('customer.explore') ? 'text-accent-600 font-semibold' : 'text-coffee-500' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <span>Explore</span>
            </a>
            <a href="{{ route('customer.reservations') }}" class="flex flex-col items-center gap-1 text-[10px] font-medium {{ request()->routeIs('customer.reservations') ? 'text-accent-600 font-semibold' : 'text-coffee-500' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span>Bookings</span>
            </a>
            <a href="{{ route('customer.favourites') }}" class="flex flex-col items-center gap-1 text-[10px] font-medium {{ request()->routeIs('customer.favourites') ? 'text-accent-600 font-semibold' : 'text-coffee-500' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                <span>Favourites</span>
            </a>
            <a href="{{ route('profile.show') }}" class="flex flex-col items-center gap-1 text-[10px] font-medium {{ request()->routeIs('profile.show') ? 'text-accent-600 font-semibold' : 'text-coffee-500' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span>Profile</span>
            </a>
        </nav>

        @stack('modals')
        @livewireScripts
    </body>
</html>
