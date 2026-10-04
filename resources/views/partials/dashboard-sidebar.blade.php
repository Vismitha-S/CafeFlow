{{-- Dashboard sidebar navigation component --}}
{{-- Role-specific navigation with warm cafe aesthetic and no Customer role badge --}}
@props(['dashboardRole' => 'customer'])

{{-- Mobile backdrop overlay --}}
<div x-show="sidebarOpen"
     x-cloak
     @click="sidebarOpen = false"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-coffee-950/60 backdrop-blur-xs z-40 lg:hidden">
</div>

{{-- Sidebar panel --}}
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
       class="fixed lg:static inset-y-0 left-0 z-50 w-64 bg-[#FAF7F2] border-r border-cream-200/90 transform lg:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col justify-between">

    <div class="flex flex-col flex-1 min-h-0">
        {{-- Logo area --}}
        <div class="flex items-center justify-between h-20 px-6 border-b border-cream-200/80">
            <a href="/" class="flex items-center gap-2.5 transition-transform duration-200 hover:scale-[1.02]" aria-label="CafeFlow Home">
                <x-cafeflow-logo class="h-8 w-auto" />
            </a>
            <button @click="sidebarOpen = false" class="lg:hidden p-1.5 rounded-xl text-coffee-400 hover:bg-cream-100 transition-colors duration-200" aria-label="Close sidebar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Role badge ONLY for Admin and Owner -- Customer badge is explicitly removed --}}
        @if($dashboardRole === 'admin')
            <div class="px-6 pt-4 pb-1">
                <span class="badge bg-red-50 text-red-700 border border-red-200/60 font-medium">Admin Panel</span>
            </div>
        @elseif($dashboardRole === 'owner')
            <div class="px-6 pt-4 pb-1">
                <span class="badge bg-sage-50 text-sage-700 border border-sage-200/60 font-medium">Cafe Owner</span>
            </div>
        @endif

        {{-- Navigation links --}}
        <nav class="flex-1 px-4 py-5 space-y-1.5 overflow-y-auto">
            @if($dashboardRole === 'admin')
                <x-dashboard-nav-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard')" icon="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                    Dashboard
                </x-dashboard-nav-link>
                <x-dashboard-nav-link href="#" icon="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z">
                    Users
                </x-dashboard-nav-link>
                <x-dashboard-nav-link href="#" icon="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                    Cafes
                </x-dashboard-nav-link>
                <x-dashboard-nav-link href="#" icon="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                    Cafe Owners
                </x-dashboard-nav-link>
                <x-dashboard-nav-link href="#" icon="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                    Reservations
                </x-dashboard-nav-link>

            @elseif($dashboardRole === 'owner')
                <x-dashboard-nav-link href="{{ route('owner.dashboard') }}" :active="request()->routeIs('owner.dashboard')" icon="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                    Dashboard
                </x-dashboard-nav-link>
                <x-dashboard-nav-link href="#" icon="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                    My Cafe
                </x-dashboard-nav-link>
                <x-dashboard-nav-link href="#" icon="M4 6h16M4 10h16M4 14h16M4 18h16">
                    Tables
                </x-dashboard-nav-link>
                <x-dashboard-nav-link href="#" icon="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                    Menu
                </x-dashboard-nav-link>
                <x-dashboard-nav-link href="#" icon="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                    Reservations
                </x-dashboard-nav-link>

            @else
                {{-- Customer personal navigation items --}}
                <x-dashboard-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard') || request()->routeIs('customer.dashboard')" icon="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                    Home
                </x-dashboard-nav-link>

                <x-dashboard-nav-link href="{{ route('customer.explore') }}" :active="request()->routeIs('customer.explore') || request()->routeIs('customer.cafe.show')" icon="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z">
                    Explore Cafes
                </x-dashboard-nav-link>

                <x-dashboard-nav-link href="{{ route('customer.reservations') }}" :active="request()->routeIs('customer.reservations') || request()->routeIs('customer.reservation.checkout')" icon="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                    My Reservations
                </x-dashboard-nav-link>

                <x-dashboard-nav-link href="{{ route('customer.favourites') }}" :active="request()->routeIs('customer.favourites')" icon="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                    Favourites
                </x-dashboard-nav-link>

                <x-dashboard-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')" icon="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                    Profile
                </x-dashboard-nav-link>

                <x-dashboard-nav-link href="{{ route('customer.settings') }}" :active="request()->routeIs('customer.settings')" icon="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                    Settings
                </x-dashboard-nav-link>
            @endif
        </nav>
    </div>

    {{-- Sidebar footer with help and logout --}}
    <div class="px-4 py-4 border-t border-cream-200/80 space-y-1">
        <a href="#" class="flex items-center gap-3 px-3.5 py-2 text-xs font-medium text-coffee-500 rounded-xl hover:bg-cream-100 hover:text-coffee-800 transition-colors duration-200">
            <svg class="w-4 h-4 text-coffee-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Help & Concierge</span>
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center gap-3 w-full px-3.5 py-2 text-xs font-medium text-coffee-500 rounded-xl hover:bg-rose-50 hover:text-rose-700 transition-colors duration-200">
                <svg class="w-4 h-4 text-coffee-400 group-hover:text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span>Log Out</span>
            </button>
        </form>
    </div>
</aside>
