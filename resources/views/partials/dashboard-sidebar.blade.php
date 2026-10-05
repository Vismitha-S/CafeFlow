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
                <x-cafeflow-logo class="h-10 w-auto" />
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
                <x-dashboard-nav-link href="{{ route('owner.cafe.edit') }}" :active="request()->routeIs('owner.cafe.*')" icon="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                    My Cafe
                </x-dashboard-nav-link>
                <x-dashboard-nav-link href="{{ route('owner.tables.index') }}" :active="request()->routeIs('owner.tables.*')" icon="M4 6h16M4 10h16M4 14h16M4 18h16">
                    Tables
                </x-dashboard-nav-link>
                <x-dashboard-nav-link href="{{ route('owner.menu.index') }}" :active="request()->routeIs('owner.menu.*')" icon="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                    Menu
                </x-dashboard-nav-link>
                <x-dashboard-nav-link href="{{ route('owner.reservations.index') }}" :active="request()->routeIs('owner.reservations.*')" icon="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                    Reservations
                </x-dashboard-nav-link>
                <x-dashboard-nav-link href="{{ route('owner.notifications.index') }}" :active="request()->routeIs('owner.notifications.*')" icon="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                    Notifications
                </x-dashboard-nav-link>
                <x-dashboard-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')" icon="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                    Profile & Settings
                </x-dashboard-nav-link>

                {{-- Switch to customer mode --}}
                <div class="pt-3 mt-3 border-t border-cream-200/80">
                    <form method="POST" action="{{ route('switch.to.customer') }}">
                        @csrf
                        <button type="submit"
                                class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-medium text-coffee-600 hover:text-coffee-950 hover:bg-cream-100 transition-colors">
                            <svg class="w-4 h-4 text-coffee-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <span>Browse as Customer</span>
                        </button>
                    </form>
                </div>

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

                {{-- Partner / Switch to Owner CTA --}}
                <div class="pt-3 mt-3 border-t border-cream-200/80">
                    <form method="POST" action="{{ route('switch.to.owner') }}">
                        @csrf
                        <button type="submit"
                                class="w-full flex flex-col text-left p-3 rounded-2xl bg-gradient-to-br from-cream-100 via-white to-accent-50/50 border border-cream-200 hover:border-accent-400 hover:shadow-subtle transition-all duration-200 group">
                            <div class="flex items-center gap-2 mb-1 text-accent-700 font-semibold text-xs">
                                <svg class="w-4 h-4 text-accent-600 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <span>Own a Cafe?</span>
                            </div>
                            <span class="text-[11px] text-coffee-600 leading-tight">Switch to Owner Portal & create your cafe</span>
                        </button>
                    </form>
                </div>
            @endif
        </nav>
    </div>

    {{-- Sidebar footer with help and logout --}}
    <div class="px-4 py-2 border-t border-cream-200/80 space-y-0.5">
        <button type="button" @click="$dispatch('open-help')" class="flex items-center gap-3 w-full px-3.5 py-2 text-xs font-medium text-coffee-500 rounded-xl hover:bg-cream-100 hover:text-coffee-800 transition-colors duration-200">
            <svg class="w-4 h-4 text-coffee-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Help & Concierge</span>
        </button>

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

{{-- ==================== Help & Concierge Slide-Over Panel ==================== --}}
<div x-data="{ helpOpen: false, activeTab: 'faq' }"
     x-on:keydown.escape.window="helpOpen = false">

    {{-- Trigger: make the sidebar button work by sharing Alpine state via $dispatch or a global --}}
    {{-- The button inside the sidebar uses @click="helpOpen = true" but it's outside this x-data scope. --}}
    {{-- We use a custom event bridge instead: --}}
    <span x-on:open-help.window="helpOpen = true"></span>

    {{-- Backdrop --}}
    <div x-show="helpOpen"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="helpOpen = false"
         style="z-index: 9998;"
         class="fixed inset-0 bg-coffee-950/60 backdrop-blur-sm">
    </div>

    {{-- Slide-over panel --}}
    <div x-show="helpOpen"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-x-full opacity-0"
         x-transition:enter-end="translate-x-0 opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-x-0 opacity-100"
         x-transition:leave-end="translate-x-full opacity-0"
         style="z-index: 9999;"
         class="fixed inset-y-0 right-0 w-full max-w-md bg-[#faf8f4] shadow-2xl flex flex-col overflow-hidden">

        {{-- Panel Header --}}
        <div class="flex items-center justify-between px-6 py-5 border-b border-cream-200 bg-white">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-accent-500 flex items-center justify-center shadow">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-serif text-base font-bold text-coffee-950">Help & Concierge</h2>
                    <p class="text-[11px] text-coffee-400">CafeFlow support centre</p>
                </div>
            </div>
            <button @click="helpOpen = false" class="p-2 rounded-xl text-coffee-400 hover:bg-cream-100 hover:text-coffee-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Tabs --}}
        <div class="flex border-b border-cream-200 bg-white px-6">
            <button @click="activeTab = 'faq'"
                    :class="activeTab === 'faq' ? 'border-b-2 border-accent-500 text-accent-600 font-semibold' : 'text-coffee-400 hover:text-coffee-700'"
                    class="py-3 mr-6 text-xs transition-colors">FAQ</button>
            <button @click="activeTab = 'contact'"
                    :class="activeTab === 'contact' ? 'border-b-2 border-accent-500 text-accent-600 font-semibold' : 'text-coffee-400 hover:text-coffee-700'"
                    class="py-3 mr-6 text-xs transition-colors">Contact Us</button>
            <button @click="activeTab = 'tips'"
                    :class="activeTab === 'tips' ? 'border-b-2 border-accent-500 text-accent-600 font-semibold' : 'text-coffee-400 hover:text-coffee-700'"
                    class="py-3 text-xs transition-colors">Quick Tips</button>
        </div>

        {{-- Scrollable content --}}
        <div class="flex-1 overflow-y-auto px-6 py-5 space-y-4">

            {{-- FAQ Tab --}}
            <div x-show="activeTab === 'faq'" class="space-y-3">
                <p class="text-[11px] text-coffee-700 uppercase tracking-widest font-semibold mb-4">Frequently Asked Questions</p>

                @php
                $faqs = [
                    ['q' => 'How do customers make a reservation?', 'a' => 'Customers browse your café page, pick a date and time, choose a table, and pay the reservation fee online. The slot is instantly confirmed.'],
                    ['q' => 'How does cancellation work?', 'a' => 'Customers can cancel a reservation. A 50% deduction is applied to the reservation fee and the remaining amount is refunded. The table slot is automatically freed.'],
                    ['q' => 'How do I add or edit tables?', 'a' => 'Go to Tables in the sidebar. You can add, edit, activate, or deactivate any table from there.'],
                    ['q' => 'How do I update my café details?', 'a' => 'Click My Cafe in the sidebar to update your café name, location, hours, reservation fee, and cancellation policy.'],
                    ['q' => 'How do I manage my menu?', 'a' => 'Go to Menu in the sidebar. You can add categories and menu items with prices, descriptions, and images.'],
                    ['q' => 'Where can I see all reservations?', 'a' => 'Click Reservations in the sidebar. You can filter by date and status, and view all guest booking details.'],
                    ['q' => 'Will I be notified of new bookings?', 'a' => 'Yes! You will receive real-time notifications in the bell icon at the top right whenever a new reservation is made.'],
                ];
                @endphp

                @foreach($faqs as $faq)
                <div x-data="{ open: false }" class="rounded-xl border border-cream-200 bg-white overflow-hidden">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-left">
                        <span class="text-xs font-semibold text-coffee-800">{{ $faq['q'] }}</span>
                        <svg :class="open ? 'rotate-180' : ''"
                             class="w-4 h-4 text-coffee-400 flex-shrink-0 ml-3 transition-transform duration-200"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="px-4 pb-4">
                        <p class="text-xs text-coffee-500 leading-relaxed">{{ $faq['a'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Contact Tab --}}
            <div x-show="activeTab === 'contact'" class="space-y-4">
                <p class="text-[11px] text-coffee-700 uppercase tracking-widest font-semibold mb-4">Get in Touch</p>

                <div class="rounded-2xl bg-white border border-cream-200 p-5 space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-accent-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <p class="text-[11px] text-coffee-700 font-semibold uppercase tracking-wide">Email Support</p>
                            <a href="mailto:support@cafeflow.com" class="text-sm font-semibold text-accent-600 hover:underline">support@cafeflow.com</a>
                            <p class="text-[11px] text-coffee-600 mt-0.5">We respond within 24 hours on business days.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-sage-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-sage-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                        <div>
                            <p class="text-[11px] text-coffee-700 font-semibold uppercase tracking-wide">Phone</p>
                            <p class="text-sm font-semibold text-coffee-900">+1 (800) CAFE-FLOW</p>
                            <p class="text-[11px] text-coffee-600 mt-0.5">Mon &ndash; Fri, 9 AM &ndash; 6 PM</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl bg-accent-50 border border-accent-100 p-4">
                    <p class="text-xs font-semibold text-accent-800">☕ CafeFlow Concierge Promise</p>
                    <p class="text-xs text-accent-700 mt-1 leading-relaxed">Every café owner deserves a smooth experience. If something isn't right, we're here to make it right — within one business day, guaranteed.</p>
                </div>
            </div>

            {{-- Tips Tab --}}
            <div x-show="activeTab === 'tips'" class="space-y-3">
                <p class="text-[11px] text-coffee-700 uppercase tracking-widest font-semibold mb-4">Quick Tips for Owners</p>

                @php
                $tips = [
                    ['icon' => '🪑', 'title' => 'Keep tables up to date', 'tip' => 'Mark tables as Inactive during private events or maintenance so guests don\'t accidentally book them.'],
                    ['icon' => '📋', 'title' => 'Complete your menu', 'tip' => 'A full menu with photos and prices builds trust with guests before they even walk through the door.'],
                    ['icon' => '🔔', 'title' => 'Check notifications daily', 'tip' => 'New reservations trigger real-time alerts. Keep an eye on the bell icon so you never miss a booking.'],
                    ['icon' => '⏰', 'title' => 'Set accurate opening hours', 'tip' => 'Guests can only book within your listed hours. Keep them updated to avoid confusion.'],
                    ['icon' => '💰', 'title' => 'Set a fair reservation fee', 'tip' => 'A small reservation fee reduces no-shows while still being accessible. Try RM 5-15 per booking.'],
                    ['icon' => '📊', 'title' => 'Review your dashboard daily', 'tip' => 'The dashboard shows today\'s bookings, upcoming reservations, and table availability at a glance.'],
                ];
                @endphp

                @foreach($tips as $tip)
                <div class="flex gap-3 rounded-xl bg-white border border-cream-200 p-4">
                    <span class="text-xl flex-shrink-0">{{ $tip['icon'] }}</span>
                    <div>
                        <p class="text-xs font-semibold text-coffee-800">{{ $tip['title'] }}</p>
                        <p class="text-[11px] text-coffee-500 leading-relaxed mt-0.5">{{ $tip['tip'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>

        </div>

        {{-- Panel Footer --}}
        <div class="px-6 py-4 border-t border-cream-200 bg-white">
            <p class="text-[10px] text-coffee-400 text-center">CafeFlow · Version 1.0 · <a href="mailto:support@cafeflow.com" class="underline hover:text-coffee-600">support@cafeflow.com</a></p>
        </div>
    </div>
</div>
