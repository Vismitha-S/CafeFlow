{{-- Owner Dashboard --}}
{{-- Displays cafe-specific statistics and management tools --}}
<x-dashboard-layout dashboard-role="owner">
    <x-slot name="title">Owner Dashboard</x-slot>
    <x-slot name="header">Owner Dashboard</x-slot>

    {{-- Welcome message --}}
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-coffee-800">Welcome back, {{ Auth::user()->name }} ☕</h2>
        <p class="text-coffee-400 mt-1">Here's what's happening at your cafe today.</p>
    </div>

    {{-- Cafe KPI stat cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-stat-card
            title="Today's Reservations"
            value="12"
            icon="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
            trend="+3"
            :trend-up="true"
            color="accent"
        />
        <x-stat-card
            title="Upcoming"
            value="28"
            icon="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
            color="blue"
        />
        <x-stat-card
            title="Tables Available"
            value="8/15"
            icon="M4 6h16M4 10h16M4 14h16M4 18h16"
            color="emerald"
        />
        <x-stat-card
            title="This Month Revenue"
            value="LKR 45,200"
            icon="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
            trend="+22%"
            :trend-up="true"
            color="amber"
        />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Today's reservations list --}}
        <div class="dashboard-card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-coffee-800">Today's Reservations</h3>
                <a href="#" class="text-sm text-accent-500 hover:text-accent-600 transition-colors duration-200">View All</a>
            </div>
            <div class="space-y-3">
                @php
                    // Mock today's reservation data
                    $todayReservations = [
                        ['customer' => 'Amara Perera', 'time' => '10:30 AM', 'guests' => 2, 'table' => 'T-3', 'status' => 'confirmed'],
                        ['customer' => 'Kavinda Silva', 'time' => '12:00 PM', 'guests' => 4, 'table' => 'T-7', 'status' => 'confirmed'],
                        ['customer' => 'Nishara De Silva', 'time' => '2:30 PM', 'guests' => 3, 'table' => 'T-5', 'status' => 'pending'],
                        ['customer' => 'Ruwan Bandara', 'time' => '4:00 PM', 'guests' => 2, 'table' => 'T-1', 'status' => 'confirmed'],
                    ];
                @endphp

                @foreach($todayReservations as $reservation)
                    <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-cream-100' : '' }}">
                        <div class="flex items-center gap-3">
                            {{-- Customer avatar initial --}}
                            <div class="w-9 h-9 bg-cream-200 rounded-full flex items-center justify-center">
                                <span class="text-coffee-600 font-semibold text-xs">{{ substr($reservation['customer'], 0, 1) }}</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-coffee-700">{{ $reservation['customer'] }}</p>
                                <p class="text-xs text-coffee-400">{{ $reservation['time'] }} · {{ $reservation['guests'] }} guests · {{ $reservation['table'] }}</p>
                            </div>
                        </div>
                        {{-- Reservation status badge --}}
                        @if($reservation['status'] === 'confirmed')
                            <span class="badge bg-emerald-50 text-emerald-600">Confirmed</span>
                        @else
                            <span class="badge bg-amber-50 text-amber-600">Pending</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Cafe quick overview --}}
        <div class="dashboard-card">
            <h3 class="font-semibold text-coffee-800 mb-4">Cafe Overview</h3>
            <div class="space-y-4">
                {{-- Cafe info card --}}
                <div class="flex items-center gap-4 p-4 bg-cream-50 rounded-xl">
                    <div class="w-16 h-16 rounded-xl overflow-hidden shrink-0">
                        <img src="https://images.unsplash.com/photo-1559925393-8be0ec4767c8?w=128&h=128&fit=crop&q=80"
                             alt="Your cafe"
                             class="w-full h-full object-cover">
                    </div>
                    <div>
                        <h4 class="font-semibold text-coffee-800">The Velvet Bean</h4>
                        <p class="text-sm text-coffee-400">Colombo 07, Sri Lanka</p>
                        <div class="flex items-center gap-1 mt-1">
                            <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <span class="text-sm font-medium text-coffee-600">4.8</span>
                            <span class="text-xs text-coffee-300">(124 reviews)</span>
                        </div>
                    </div>
                </div>

                {{-- Quick stats for the owner --}}
                <div class="grid grid-cols-3 gap-3">
                    <div class="text-center p-3 bg-cream-50 rounded-xl">
                        <p class="text-lg font-bold text-coffee-800">15</p>
                        <p class="text-xs text-coffee-400">Tables</p>
                    </div>
                    <div class="text-center p-3 bg-cream-50 rounded-xl">
                        <p class="text-lg font-bold text-coffee-800">32</p>
                        <p class="text-xs text-coffee-400">Menu Items</p>
                    </div>
                    <div class="text-center p-3 bg-cream-50 rounded-xl">
                        <p class="text-lg font-bold text-coffee-800">4.8</p>
                        <p class="text-xs text-coffee-400">Rating</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>
