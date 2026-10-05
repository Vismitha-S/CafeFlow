{{-- Owner Dashboard --}}
{{-- Clean, responsive vintage artisanal CafeFlow owner experience --}}
<x-dashboard-layout dashboard-role="owner">
    <x-slot name="title">Owner Dashboard - {{ $cafe->name }}</x-slot>
    <x-slot name="header">Owner Dashboard</x-slot>

    <div class="space-y-8" x-data="{ resTab: 'today' }">

        {{-- Welcome & Cafe Status Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-cream-200">
            <div>
                <div class="flex items-center gap-2.5">
                    <h1 class="font-serif text-2xl sm:text-3xl font-bold text-coffee-950 tracking-tight">
                        Welcome back, {{ $owner->name }}
                    </h1>
                    <span class="text-xl">☕</span>
                </div>
                <p class="text-xs sm:text-sm text-coffee-500 mt-1">
                    Overview and live management for <span class="font-semibold text-coffee-900">{{ $cafe->name }}</span> ({{ $cafe->city }}).
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('customer.cafe.show', $cafe->slug) }}" target="_blank" class="btn-secondary text-xs py-2 px-3.5 hidden sm:inline-flex">
                    <span>Public Storefront</span>
                    <span>&rarr;</span>
                </a>

                <div class="flex items-center gap-2">
                    @if($cafe->status === 'active')
                        <span class="badge-sage text-xs font-semibold px-3 py-1.5 shadow-2xs">
                            <span class="w-2 h-2 rounded-full bg-sage-500 animate-pulse"></span>
                            <span>Cafe Active</span>
                        </span>
                    @else
                        <span class="badge bg-amber-50 text-amber-700 border border-amber-200/80 text-xs font-semibold px-3 py-1.5">
                            <span>Cafe Inactive</span>
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Quick Actions Bar --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
            <a href="{{ route('owner.cafe.edit') }}" class="glass-card rounded-2xl p-4 flex items-center gap-3.5 hover:border-cream-300 hover:shadow-card transition-all duration-200 group">
                <div class="w-11 h-11 rounded-xl bg-accent-50 text-accent-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-coffee-950 truncate">Manage Cafe</p>
                    <p class="text-[11px] text-coffee-500 truncate">Policy & hours</p>
                </div>
            </a>

            <a href="{{ route('owner.tables.index') }}" class="glass-card rounded-2xl p-4 flex items-center gap-3.5 hover:border-cream-300 hover:shadow-card transition-all duration-200 group">
                <div class="w-11 h-11 rounded-xl bg-sage-50 text-sage-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-coffee-950 truncate">Add / View Tables</p>
                    <p class="text-[11px] text-coffee-500 truncate">{{ $totalTablesCount }} Tables ({{ $totalCapacity }} seats)</p>
                </div>
            </a>

            <a href="{{ route('owner.menu.index') }}" class="glass-card rounded-2xl p-4 flex items-center gap-3.5 hover:border-cream-300 hover:shadow-card transition-all duration-200 group">
                <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-coffee-950 truncate">Add Menu Item</p>
                    <p class="text-[11px] text-coffee-500 truncate">{{ $menuItemsCount }} Items across {{ $categoriesCount }} cats</p>
                </div>
            </a>

            <a href="{{ route('owner.reservations.index') }}" class="glass-card rounded-2xl p-4 flex items-center gap-3.5 hover:border-cream-300 hover:shadow-card transition-all duration-200 group">
                <div class="w-11 h-11 rounded-xl bg-coffee-100 text-coffee-800 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-coffee-950 truncate">View Reservations</p>
                    <p class="text-[11px] text-coffee-500 truncate">{{ $todayCount }} today • {{ $upcomingCount }} upcoming</p>
                </div>
            </a>
        </div>

        {{-- 4 Primary KPI Stat Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Today's Reservations --}}
            <div class="dashboard-card group relative overflow-hidden">
                <div class="flex items-start justify-between">
                    <div class="w-11 h-11 bg-accent-50 rounded-xl flex items-center justify-center text-accent-600 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <span class="badge-sage text-[11px] font-semibold">{{ $todayConfirmedCount }} Confirmed</span>
                </div>
                <div class="mt-4">
                    <p class="text-3xl font-serif font-bold text-coffee-950">{{ $todayCount }}</p>
                    <p class="text-xs text-coffee-500 mt-1">Today's Reservations</p>
                </div>
            </div>

            {{-- Upcoming Reservations --}}
            <div class="dashboard-card group relative overflow-hidden">
                <div class="flex items-start justify-between">
                    <div class="w-11 h-11 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="badge bg-blue-50 text-blue-700 border border-blue-200/80 text-[11px] font-semibold">Future Bookings</span>
                </div>
                <div class="mt-4">
                    <p class="text-3xl font-serif font-bold text-coffee-950">{{ $upcomingCount }}</p>
                    <p class="text-xs text-coffee-500 mt-1">Upcoming Reservations</p>
                </div>
            </div>

            {{-- Active Tables & Capacity --}}
            <div class="dashboard-card group relative overflow-hidden">
                <div class="flex items-start justify-between">
                    <div class="w-11 h-11 bg-sage-50 rounded-xl flex items-center justify-center text-sage-600 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    </div>
                    <span class="badge-coffee text-[11px] font-semibold">{{ $totalCapacity }} Seats</span>
                </div>
                <div class="mt-4">
                    <p class="text-3xl font-serif font-bold text-coffee-950">{{ $activeTablesCount }}/{{ $totalTablesCount }}</p>
                    <p class="text-xs text-coffee-500 mt-1">Active Seating Tables</p>
                </div>
            </div>

            {{-- Monthly Revenue --}}
            <div class="dashboard-card group relative overflow-hidden">
                <div class="flex items-start justify-between">
                    <div class="w-11 h-11 bg-amber-50 rounded-xl flex items-center justify-center text-amber-600 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="badge bg-amber-50 text-amber-700 border border-amber-200/80 text-[11px] font-semibold">This Month</span>
                </div>
                <div class="mt-4">
                    <p class="text-2xl font-serif font-bold text-coffee-950">LKR {{ number_format($monthlyRevenue, 2) }}</p>
                    <p class="text-xs text-coffee-500 mt-1">Reservation Deposit Revenue</p>
                </div>
            </div>
        </div>

        {{-- 2-Column Section: Cafe Overview & Operating Hours --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            {{-- Cafe Details Card (7 columns) --}}
            <div class="lg:col-span-7 dashboard-card space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-cream-200">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-accent-50 text-accent-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <h2 class="font-serif text-lg font-bold text-coffee-950">Cafe Overview & Policy</h2>
                    </div>
                    <a href="{{ route('owner.cafe.edit') }}" class="text-xs font-semibold text-accent-600 hover:text-accent-700 flex items-center gap-1">
                        <span>Edit Cafe</span>
                        <span>&rarr;</span>
                    </a>
                </div>

                <div class="flex flex-col sm:flex-row items-start gap-4">
                    <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl overflow-hidden bg-cream-100 shrink-0 border border-cream-200 shadow-2xs">
                        <img src="{{ $cafe->image_path ?: 'https://images.unsplash.com/photo-1554118811-1e0d58224f24?auto=format&fit=crop&w=400&q=80' }}"
                             alt="{{ $cafe->name }}"
                             class="w-full h-full object-cover">
                    </div>

                    <div class="space-y-1.5 min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <h3 class="font-serif text-xl font-bold text-coffee-950 truncate">{{ $cafe->name }}</h3>
                            <span class="badge-sage text-[10px] font-semibold uppercase">{{ $cafe->status }}</span>
                        </div>
                        <p class="text-xs text-coffee-600 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-coffee-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                            <span class="truncate">{{ $cafe->address }}, {{ $cafe->city }}</span>
                        </p>
                        @if($cafe->phone || $cafe->email)
                            <div class="text-xs text-coffee-500 flex flex-wrap items-center gap-3 pt-0.5">
                                @if($cafe->phone)
                                    <span>📞 {{ $cafe->phone }}</span>
                                @endif
                                @if($cafe->email)
                                    <span>✉️ {{ $cafe->email }}</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Policy Summary Pills --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 pt-1">
                    <div class="p-3 bg-cream-50 rounded-xl border border-cream-200">
                        <span class="text-[11px] text-coffee-400 block font-sans">Reservation Fee</span>
                        <span class="font-serif text-base font-bold text-coffee-900">LKR {{ number_format($cafe->reservation_fee, 2) }}</span>
                    </div>
                    <div class="p-3 bg-cream-50 rounded-xl border border-cream-200">
                        <span class="text-[11px] text-coffee-400 block font-sans">Cancellation Penalty</span>
                        <span class="font-serif text-base font-bold text-rose-700">50% fee</span>
                    </div>
                    <div class="p-3 bg-cream-50 rounded-xl border border-cream-200 col-span-2 sm:col-span-1">
                        <span class="text-[11px] text-coffee-400 block font-sans">Slot Release</span>
                        <span class="font-serif text-sm font-bold text-sage-700">Instant</span>
                    </div>
                </div>

                {{-- Policy Notice --}}
                <div class="p-3.5 bg-sage-50/70 rounded-2xl border border-sage-200/70 text-xs text-coffee-700 space-y-1">
                    <div class="flex items-center gap-1.5 font-bold text-sage-900 text-xs">
                        <svg class="w-4 h-4 text-sage-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Active Cancellation Policy:</span>
                    </div>
                    <p class="text-[11px] text-coffee-600 leading-relaxed pl-5">
                        A 50% cancellation fee will be deducted upon customer cancellation. The remaining 50% balance is refunded and the table slot is immediately made available back for new bookings.
                    </p>
                </div>
            </div>

            {{-- Operating Hours Card (5 columns) --}}
            <div class="lg:col-span-5 dashboard-card space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-cream-200">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-sage-50 text-sage-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h2 class="font-serif text-lg font-bold text-coffee-950">Operating Hours</h2>
                    </div>
                    <span class="text-xs text-coffee-400 font-sans">Weekly Schedule</span>
                </div>

                @php
                    $dayNames = [
                        1 => 'Monday',
                        2 => 'Tuesday',
                        3 => 'Wednesday',
                        4 => 'Thursday',
                        5 => 'Friday',
                        6 => 'Saturday',
                        7 => 'Sunday',
                    ];
                    $todayIso = (int) now()->isoWeekday();
                @endphp

                <div class="space-y-2 text-xs">
                    @forelse($hours as $hour)
                        <div class="flex items-center justify-between py-2 px-3 rounded-xl transition-colors {{ $hour->day_of_week === $todayIso ? 'bg-cream-100 font-semibold border border-cream-300' : 'hover:bg-cream-50' }}">
                            <span class="text-coffee-800 flex items-center gap-1.5">
                                <span>{{ $dayNames[$hour->day_of_week] ?? ('Day '.$hour->day_of_week) }}</span>
                                @if($hour->day_of_week === $todayIso)
                                    <span class="text-[10px] text-accent-600 font-bold">(Today)</span>
                                @endif
                            </span>

                            @if($hour->is_closed)
                                <span class="text-[11px] text-rose-600 font-medium">Closed</span>
                            @else
                                <span class="text-[11px] text-coffee-600 font-mono">
                                    {{ \Carbon\Carbon::parse($hour->opens_at)->format('g:i A') }} - {{ \Carbon\Carbon::parse($hour->closes_at)->format('g:i A') }}
                                </span>
                            @endif
                        </div>
                    @empty
                        <p class="text-xs text-coffee-400 italic py-4 text-center">No operating hours configured.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Reservations Section --}}
        <div class="dashboard-card space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-cream-200">
                <div>
                    <h2 class="font-serif text-xl font-bold text-coffee-950">Recent & Today's Bookings</h2>
                    <p class="text-xs text-coffee-500 mt-0.5">Real-time reservation requests and confirmed table bookings.</p>
                </div>

                <div class="flex items-center gap-2">
                    <div class="flex items-center gap-1.5 bg-cream-100/80 p-1 rounded-xl text-xs font-semibold">
                        <button type="button" @click="resTab = 'today'"
                                :class="resTab === 'today' ? 'bg-white text-coffee-900 shadow-xs' : 'text-coffee-600 hover:text-coffee-900'"
                                class="px-3 py-1.5 rounded-lg transition-all">
                            Today ({{ $todayCount }})
                        </button>
                        <button type="button" @click="resTab = 'upcoming'"
                                :class="resTab === 'upcoming' ? 'bg-white text-coffee-900 shadow-xs' : 'text-coffee-600 hover:text-coffee-900'"
                                class="px-3 py-1.5 rounded-lg transition-all">
                            Upcoming ({{ $upcomingCount }})
                        </button>
                        <button type="button" @click="resTab = 'recent'"
                                :class="resTab === 'recent' ? 'bg-white text-coffee-900 shadow-xs' : 'text-coffee-600 hover:text-coffee-900'"
                                class="px-3 py-1.5 rounded-lg transition-all">
                            Recent ({{ $recentReservations->count() }})
                        </button>
                    </div>

                    <a href="{{ route('owner.reservations.index') }}" class="btn-ghost text-xs hidden sm:inline-flex">
                        All Bookings &rarr;
                    </a>
                </div>
            </div>

            {{-- Tab 1: Today's Reservations --}}
            <div x-show="resTab === 'today'" class="space-y-3">
                @forelse($todayReservations as $res)
                    <div class="p-4 rounded-2xl border border-cream-200 hover:border-cream-300 bg-cream-50/40 hover:bg-white transition-all flex flex-col md:flex-row md:items-center justify-between gap-3">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <div class="w-10 h-10 rounded-xl bg-accent-100 text-accent-800 font-serif font-bold text-sm flex items-center justify-center shrink-0">
                                {{ strtoupper(substr($res->user?->name ?? 'Guest', 0, 2)) }}
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <p class="font-serif text-sm font-bold text-coffee-950 truncate">{{ $res->user?->name ?? 'Guest Customer' }}</p>
                                    <span class="text-[11px] text-coffee-400 font-mono">#RES-{{ $res->id }}</span>
                                </div>
                                <p class="text-xs text-coffee-600 flex items-center gap-2 mt-0.5">
                                    <span>⏰ {{ \Carbon\Carbon::parse($res->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($res->end_time)->format('g:i A') }}</span>
                                    <span>•</span>
                                    <span>{{ $res->cafeTable?->name ?: ('Table '.$res->cafeTable?->table_number) }} ({{ ucfirst($res->cafeTable?->location ?? 'indoor') }})</span>
                                    <span>•</span>
                                    <span>👥 {{ $res->guest_count }} Guests</span>
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between md:justify-end gap-3 pt-2 md:pt-0 border-t md:border-t-0 border-cream-200">
                            <span class="text-xs font-semibold text-coffee-800">
                                Deposit: LKR {{ number_format($res->reservation_fee, 2) }}
                            </span>
                            @if($res->status === 'confirmed')
                                <span class="badge-sage text-xs font-semibold">Confirmed</span>
                            @elseif($res->status === 'pending')
                                <span class="badge bg-amber-50 text-amber-700 border border-amber-200/80 text-xs font-semibold">Pending</span>
                            @elseif($res->status === 'completed')
                                <span class="badge bg-cream-100 text-coffee-700 border border-cream-200 text-xs font-semibold">Completed</span>
                            @else
                                <span class="badge bg-rose-50 text-rose-700 border border-rose-200/80 text-xs font-semibold">Cancelled</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 px-4 bg-cream-50/60 rounded-2xl border border-dashed border-cream-300">
                        <p class="text-sm font-serif font-bold text-coffee-900">No reservations booked for today yet</p>
                        <p class="text-xs text-coffee-500 mt-1">New customer bookings for today will appear here in real-time.</p>
                    </div>
                @endforelse
            </div>

            {{-- Tab 2: Upcoming Reservations --}}
            <div x-show="resTab === 'upcoming'" x-cloak class="space-y-3">
                @forelse($upcomingReservations as $res)
                    <div class="p-4 rounded-2xl border border-cream-200 hover:border-cream-300 bg-cream-50/40 hover:bg-white transition-all flex flex-col md:flex-row md:items-center justify-between gap-3">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-800 font-serif font-bold text-sm flex items-center justify-center shrink-0">
                                {{ strtoupper(substr($res->user?->name ?? 'Guest', 0, 2)) }}
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <p class="font-serif text-sm font-bold text-coffee-950 truncate">{{ $res->user?->name ?? 'Guest Customer' }}</p>
                                    <span class="text-[11px] text-coffee-400 font-mono">#RES-{{ $res->id }}</span>
                                </div>
                                <p class="text-xs text-coffee-600 flex items-center gap-2 mt-0.5">
                                    <span>📅 {{ \Carbon\Carbon::parse($res->reservation_date)->format('D, M j, Y') }}</span>
                                    <span>•</span>
                                    <span>⏰ {{ \Carbon\Carbon::parse($res->start_time)->format('g:i A') }}</span>
                                    <span>•</span>
                                    <span>{{ $res->cafeTable?->name ?: ('Table '.$res->cafeTable?->table_number) }}</span>
                                    <span>•</span>
                                    <span>👥 {{ $res->guest_count }} Guests</span>
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between md:justify-end gap-3 pt-2 md:pt-0 border-t md:border-t-0 border-cream-200">
                            <span class="text-xs font-semibold text-coffee-800">
                                Deposit: LKR {{ number_format($res->reservation_fee, 2) }}
                            </span>
                            <span class="badge-sage text-xs font-semibold">{{ ucfirst($res->status) }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 px-4 bg-cream-50/60 rounded-2xl border border-dashed border-cream-300">
                        <p class="text-sm font-serif font-bold text-coffee-900">No upcoming reservations</p>
                        <p class="text-xs text-coffee-500 mt-1">Future table bookings will appear here.</p>
                    </div>
                @endforelse
            </div>

            {{-- Tab 3: Recent Reservations --}}
            <div x-show="resTab === 'recent'" x-cloak class="space-y-3">
                @forelse($recentReservations as $res)
                    <div class="p-4 rounded-2xl border border-cream-200 hover:border-cream-300 bg-cream-50/40 hover:bg-white transition-all flex flex-col md:flex-row md:items-center justify-between gap-3">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <div class="w-10 h-10 rounded-xl bg-coffee-100 text-coffee-800 font-serif font-bold text-sm flex items-center justify-center shrink-0">
                                {{ strtoupper(substr($res->user?->name ?? 'Guest', 0, 2)) }}
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <p class="font-serif text-sm font-bold text-coffee-950 truncate">{{ $res->user?->name ?? 'Guest Customer' }}</p>
                                    <span class="text-[11px] text-coffee-400 font-mono">#RES-{{ $res->id }}</span>
                                </div>
                                <p class="text-xs text-coffee-600 flex items-center gap-2 mt-0.5">
                                    <span>📅 {{ \Carbon\Carbon::parse($res->reservation_date)->format('M j, Y') }}</span>
                                    <span>•</span>
                                    <span>⏰ {{ \Carbon\Carbon::parse($res->start_time)->format('g:i A') }}</span>
                                    <span>•</span>
                                    <span>{{ $res->cafeTable?->name ?: ('Table '.$res->cafeTable?->table_number) }}</span>
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between md:justify-end gap-3 pt-2 md:pt-0 border-t md:border-t-0 border-cream-200">
                            <span class="text-xs font-semibold text-coffee-800">
                                Fee: LKR {{ number_format($res->reservation_fee, 2) }}
                            </span>
                            @if($res->status === 'confirmed')
                                <span class="badge-sage text-xs font-semibold">Confirmed</span>
                            @elseif($res->status === 'completed')
                                <span class="badge bg-cream-100 text-coffee-700 border border-cream-200 text-xs font-semibold">Completed</span>
                            @elseif($res->status === 'cancelled')
                                <span class="badge bg-rose-50 text-rose-700 border border-rose-200/80 text-xs font-semibold">Cancelled</span>
                            @else
                                <span class="badge bg-amber-50 text-amber-700 border border-amber-200/80 text-xs font-semibold">Pending</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 px-4 bg-cream-50/60 rounded-2xl border border-dashed border-cream-300">
                        <p class="text-sm font-serif font-bold text-coffee-900">No reservations found</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- 2-Column Section: Table Inventory & Menu Offerings --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
            {{-- Table Inventory Summary --}}
            <div class="dashboard-card space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-cream-200">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-sage-50 text-sage-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        </div>
                        <div>
                            <h2 class="font-serif text-lg font-bold text-coffee-950">Table Inventory</h2>
                            <p class="text-[11px] text-coffee-500">{{ $activeTablesCount }} Active Tables • {{ $totalCapacity }} Total Capacity</p>
                        </div>
                    </div>
                    <a href="{{ route('owner.tables.index') }}" class="text-xs font-semibold text-accent-600 hover:text-accent-700">
                        Manage Tables &rarr;
                    </a>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-center text-xs">
                    <div class="p-2.5 bg-cream-50 rounded-xl border border-cream-200">
                        <span class="font-bold text-coffee-900 block text-sm">{{ $indoorCount }}</span>
                        <span class="text-[10px] text-coffee-500">Indoor</span>
                    </div>
                    <div class="p-2.5 bg-cream-50 rounded-xl border border-cream-200">
                        <span class="font-bold text-coffee-900 block text-sm">{{ $outdoorCount }}</span>
                        <span class="text-[10px] text-coffee-500">Outdoor</span>
                    </div>
                    <div class="p-2.5 bg-cream-50 rounded-xl border border-cream-200">
                        <span class="font-bold text-coffee-900 block text-sm">{{ $rooftopCount }}</span>
                        <span class="text-[10px] text-coffee-500">Rooftop</span>
                    </div>
                    <div class="p-2.5 bg-cream-50 rounded-xl border border-cream-200">
                        <span class="font-bold text-coffee-900 block text-sm">{{ $verandahCount }}</span>
                        <span class="text-[10px] text-coffee-500">Verandah</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @forelse($tables->take(6) as $table)
                        <div class="p-3 bg-cream-50/60 rounded-xl border border-cream-200 flex items-center justify-between">
                            <div>
                                <p class="font-serif text-xs font-bold text-coffee-950">{{ $table->name ?: ('Table '.$table->table_number) }}</p>
                                <p class="text-[11px] text-coffee-500 capitalize">{{ $table->location }} • {{ $table->capacity }} Guests</p>
                            </div>
                            <span class="{{ $table->status === 'active' ? 'badge-sage' : 'badge bg-cream-200 text-coffee-600' }} text-[10px] font-semibold capitalize">
                                {{ $table->status }}
                            </span>
                        </div>
                    @empty
                        <p class="text-xs text-coffee-400 italic py-4 col-span-2 text-center">No tables configured yet.</p>
                    @endforelse
                </div>
            </div>

            {{-- Menu Offerings Summary --}}
            <div class="dashboard-card space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-cream-200">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <div>
                            <h2 class="font-serif text-lg font-bold text-coffee-950">Menu Offerings</h2>
                            <p class="text-[11px] text-coffee-500">{{ $menuItemsCount }} Items across {{ $categoriesCount }} Categories</p>
                        </div>
                    </div>
                    <a href="{{ route('owner.menu.index') }}" class="text-xs font-semibold text-accent-600 hover:text-accent-700">
                        Manage Menu &rarr;
                    </a>
                </div>

                <div class="flex flex-wrap gap-1.5 text-xs">
                    @forelse($menuCategories as $cat)
                        <span class="px-2.5 py-1 rounded-full bg-cream-100 text-coffee-800 border border-cream-200/80 text-[11px] font-medium">
                            {{ $cat->name }} <span class="text-coffee-400">({{ $cat->menu_items_count }})</span>
                        </span>
                    @empty
                        <span class="text-xs text-coffee-400 italic">No categories created yet.</span>
                    @endforelse
                </div>

                <div class="space-y-2.5 max-h-72 overflow-y-auto pr-1">
                    @forelse($menuItems->take(5) as $item)
                        <div class="p-2.5 bg-cream-50/60 rounded-xl border border-cream-200 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <p class="font-serif text-xs font-bold text-coffee-950 truncate">{{ $item->name }}</p>
                                    @if($item->category)
                                        <span class="text-[10px] text-accent-700 bg-accent-50 px-1.5 py-0.5 rounded">{{ $item->category->name }}</span>
                                    @endif
                                </div>
                                <p class="text-[11px] text-coffee-500 truncate max-w-xs">{{ $item->description }}</p>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="font-semibold text-xs text-coffee-900">LKR {{ number_format($item->price, 2) }}</p>
                                <span class="text-[10px] {{ $item->is_available ? 'text-sage-600' : 'text-rose-500' }} font-medium">
                                    {{ $item->is_available ? 'Available' : 'Unavailable' }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-coffee-400 italic py-4 text-center">No menu items added yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</x-dashboard-layout>
