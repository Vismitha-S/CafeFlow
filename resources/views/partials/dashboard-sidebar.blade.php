{{-- Dashboard sidebar navigation component --}}
{{-- Renders role-specific navigation links based on the $dashboardRole variable --}}
@props(['dashboardRole' => 'customer'])

{{-- Mobile overlay --}}
<div x-show="sidebarOpen"
     x-cloak
     @click="sidebarOpen = false"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-coffee-900/50 z-40 lg:hidden">
</div>

{{-- Sidebar panel --}}
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
       class="fixed lg:static inset-y-0 left-0 z-50 w-72 bg-white border-r border-cream-200 transform lg:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col">

    {{-- Logo area --}}
    <div class="flex items-center justify-between h-16 px-6 border-b border-cream-200">
        <a href="/" class="transition-transform duration-200 hover:scale-105">
            <x-cafeflow-logo class="h-7 w-auto" />
        </a>
        <button @click="sidebarOpen = false" class="lg:hidden p-1.5 rounded-lg text-coffee-400 hover:bg-cream-100 transition-colors duration-200" aria-label="Close sidebar">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Role indicator badge --}}
    <div class="px-6 py-3">
        @if($dashboardRole === 'admin')
            <span class="badge bg-red-50 text-red-600">Admin Panel</span>
        @elseif($dashboardRole === 'owner')
            <span class="badge bg-emerald-50 text-emerald-600">Cafe Owner</span>
        @else
            <span class="badge bg-blue-50 text-blue-600">Customer</span>
        @endif
    </div>

    {{-- Navigation links --}}
    <nav class="flex-1 px-4 py-2 space-y-1 overflow-y-auto">
        @if($dashboardRole === 'admin')
            {{-- Admin navigation links --}}
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
            <x-dashboard-nav-link href="#" icon="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                Analytics
            </x-dashboard-nav-link>

        @elseif($dashboardRole === 'owner')
            {{-- Owner navigation links --}}
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
            <x-dashboard-nav-link href="#" icon="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                Analytics
            </x-dashboard-nav-link>

        @else
            {{-- Customer navigation links --}}
            <x-dashboard-nav-link href="{{ route('customer.dashboard') }}" :active="request()->routeIs('customer.dashboard')" icon="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                Dashboard
            </x-dashboard-nav-link>
            <x-dashboard-nav-link href="#" icon="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                My Reservations
            </x-dashboard-nav-link>
            <x-dashboard-nav-link href="#" icon="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                Favourites
            </x-dashboard-nav-link>
            <x-dashboard-nav-link href="{{ route('profile.show') }}" icon="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                Profile
            </x-dashboard-nav-link>
            <x-dashboard-nav-link href="#" icon="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                Settings
            </x-dashboard-nav-link>
        @endif
    </nav>

    {{-- Sidebar footer with logout --}}
    <div class="px-4 py-4 border-t border-cream-200">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center gap-3 w-full px-3 py-2.5 text-sm font-medium text-coffee-400 rounded-xl hover:bg-red-50 hover:text-red-600 transition-colors duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Log Out
            </button>
        </form>
    </div>
</aside>
