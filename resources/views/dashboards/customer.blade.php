{{-- Customer Dashboard --}}
{{-- Displays personal reservations, favourites, and account overview --}}
<x-dashboard-layout>
    <x-slot name="title">My Dashboard</x-slot>
    <x-slot name="header">My Dashboard</x-slot>

    @include('partials.dashboard-sidebar', ['dashboardRole' => 'customer'])

    {{-- Welcome message --}}
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-coffee-800">Hey, {{ Auth::user()->name }}! ☕</h2>
        <p class="text-coffee-400 mt-1">Ready to discover your next favourite cafe?</p>
    </div>

    {{-- Customer KPI stat cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <x-stat-card
            title="Upcoming Reservations"
            value="3"
            icon="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
            color="accent"
        />
        <x-stat-card
            title="Total Visits"
            value="18"
            icon="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
            color="blue"
        />
        <x-stat-card
            title="Favourite Cafes"
            value="5"
            icon="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
            color="red"
        />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Upcoming reservations --}}
        <div class="dashboard-card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-coffee-800">Upcoming Reservations</h3>
                <a href="#" class="text-sm text-accent-500 hover:text-accent-600 transition-colors duration-200">View All</a>
            </div>
            <div class="space-y-3">
                @php
                    // Mock upcoming reservations for the customer
                    $upcomingReservations = [
                        ['cafe' => 'The Velvet Bean', 'date' => 'Tomorrow', 'time' => '10:30 AM', 'guests' => 2, 'status' => 'confirmed'],
                        ['cafe' => 'Brew & Beyond', 'date' => 'Oct 8', 'time' => '2:00 PM', 'guests' => 4, 'status' => 'confirmed'],
                        ['cafe' => 'Sunrise Roasters', 'date' => 'Oct 12', 'time' => '11:00 AM', 'guests' => 2, 'status' => 'pending'],
                    ];
                @endphp

                @foreach($upcomingReservations as $reservation)
                    <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-cream-100' : '' }}">
                        <div class="flex items-center gap-3">
                            {{-- Cafe icon --}}
                            <div class="w-10 h-10 bg-accent-50 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-accent-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-coffee-700">{{ $reservation['cafe'] }}</p>
                                <p class="text-xs text-coffee-400">{{ $reservation['date'] }} at {{ $reservation['time'] }} · {{ $reservation['guests'] }} guests</p>
                            </div>
                        </div>
                        @if($reservation['status'] === 'confirmed')
                            <span class="badge bg-emerald-50 text-emerald-600">Confirmed</span>
                        @else
                            <span class="badge bg-amber-50 text-amber-600">Pending</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Favourite cafes --}}
        <div class="dashboard-card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-coffee-800">Favourite Cafes</h3>
                <a href="#" class="text-sm text-accent-500 hover:text-accent-600 transition-colors duration-200">Browse More</a>
            </div>
            <div class="space-y-3">
                @php
                    // Mock favourite cafe data for the customer
                    $favouriteCafes = [
                        ['name' => 'The Velvet Bean', 'location' => 'Colombo 07', 'rating' => 4.8, 'image' => 'https://images.unsplash.com/photo-1559925393-8be0ec4767c8?w=80&h=80&fit=crop&q=80'],
                        ['name' => 'Brew & Beyond', 'location' => 'Kandy', 'rating' => 4.6, 'image' => 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=80&h=80&fit=crop&q=80'],
                        ['name' => 'Sunrise Roasters', 'location' => 'Galle', 'rating' => 4.9, 'image' => 'https://images.unsplash.com/photo-1445116572660-236099ec97a0?w=80&h=80&fit=crop&q=80'],
                    ];
                @endphp

                @foreach($favouriteCafes as $cafe)
                    <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-cream-100' : '' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl overflow-hidden shrink-0">
                                <img src="{{ $cafe['image'] }}" alt="{{ $cafe['name'] }}" class="w-full h-full object-cover" loading="lazy">
                            </div>
                            <div>
                                <p class="text-sm font-medium text-coffee-700">{{ $cafe['name'] }}</p>
                                <p class="text-xs text-coffee-400">{{ $cafe['location'] }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-1">
                            <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <span class="text-sm font-medium text-coffee-600">{{ $cafe['rating'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Recent reservation history --}}
    <div class="mt-6">
        <div class="dashboard-card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-coffee-800">Reservation History</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-cream-200">
                            <th class="text-left py-3 px-2 font-medium text-coffee-500">Cafe</th>
                            <th class="text-left py-3 px-2 font-medium text-coffee-500">Date</th>
                            <th class="text-left py-3 px-2 font-medium text-coffee-500 hidden sm:table-cell">Guests</th>
                            <th class="text-left py-3 px-2 font-medium text-coffee-500">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            // Mock reservation history
                            $history = [
                                ['cafe' => 'The Velvet Bean', 'date' => 'Sep 28, 2026', 'guests' => 2, 'status' => 'completed'],
                                ['cafe' => 'Brew & Beyond', 'date' => 'Sep 20, 2026', 'guests' => 3, 'status' => 'completed'],
                                ['cafe' => 'Sunrise Roasters', 'date' => 'Sep 14, 2026', 'guests' => 4, 'status' => 'cancelled'],
                            ];
                        @endphp

                        @foreach($history as $item)
                            <tr class="border-b border-cream-50 hover:bg-cream-50 transition-colors duration-150">
                                <td class="py-3 px-2 font-medium text-coffee-700">{{ $item['cafe'] }}</td>
                                <td class="py-3 px-2 text-coffee-400">{{ $item['date'] }}</td>
                                <td class="py-3 px-2 text-coffee-400 hidden sm:table-cell">{{ $item['guests'] }}</td>
                                <td class="py-3 px-2">
                                    @if($item['status'] === 'completed')
                                        <span class="badge bg-emerald-50 text-emerald-600">Completed</span>
                                    @else
                                        <span class="badge bg-red-50 text-red-500">Cancelled</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-dashboard-layout>
