{{-- Admin Dashboard --}}
{{-- Displays platform-wide statistics and management shortcuts --}}
<x-dashboard-layout>
    <x-slot name="title">Admin Dashboard</x-slot>
    <x-slot name="header">Admin Dashboard</x-slot>

    @include('partials.dashboard-sidebar', ['dashboardRole' => 'admin'])

    {{-- Welcome message --}}
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-coffee-800">Welcome back, {{ Auth::user()->name }} 👋</h2>
        <p class="text-coffee-400 mt-1">Here's an overview of your platform activity.</p>
    </div>

    {{-- Platform KPI stat cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-stat-card
            title="Total Users"
            value="1,284"
            icon="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"
            trend="+12%"
            :trend-up="true"
            color="blue"
        />
        <x-stat-card
            title="Cafe Owners"
            value="48"
            icon="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
            trend="+5"
            :trend-up="true"
            color="emerald"
        />
        <x-stat-card
            title="Active Cafes"
            value="152"
            icon="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
            trend="+8"
            :trend-up="true"
            color="accent"
        />
        <x-stat-card
            title="Reservations"
            value="3,421"
            icon="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
            trend="+18%"
            :trend-up="true"
            color="amber"
        />
    </div>

    {{-- Recent activity and quick actions --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Recent activity feed --}}
        <div class="dashboard-card">
            <h3 class="font-semibold text-coffee-800 mb-4">Recent Activity</h3>
            <div class="space-y-4">
                @php
                    // Mock recent activity data
                    $activities = [
                        ['action' => 'New user registered', 'user' => 'Kavinda Jayawardena', 'time' => '2 minutes ago', 'color' => 'blue'],
                        ['action' => 'Cafe approved', 'user' => 'The Velvet Bean', 'time' => '15 minutes ago', 'color' => 'emerald'],
                        ['action' => 'Reservation completed', 'user' => 'Amara Perera', 'time' => '1 hour ago', 'color' => 'accent'],
                        ['action' => 'New cafe owner registered', 'user' => 'Dinesh Fernando', 'time' => '3 hours ago', 'color' => 'amber'],
                        ['action' => 'User account deactivated', 'user' => 'Test Account', 'time' => '5 hours ago', 'color' => 'red'],
                    ];
                @endphp

                @foreach($activities as $activity)
                    <div class="flex items-center gap-3 py-2 {{ !$loop->last ? 'border-b border-cream-100' : '' }}">
                        <div class="w-2 h-2 rounded-full bg-{{ $activity['color'] }}-500 shrink-0"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-coffee-700 truncate">
                                <span class="font-medium">{{ $activity['action'] }}</span> — {{ $activity['user'] }}
                            </p>
                        </div>
                        <span class="text-xs text-coffee-300 whitespace-nowrap">{{ $activity['time'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Quick management shortcuts --}}
        <div class="dashboard-card">
            <h3 class="font-semibold text-coffee-800 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-2 gap-3">
                @php
                    // Admin quick action buttons
                    $quickActions = [
                        ['label' => 'Manage Users', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'color' => 'blue'],
                        ['label' => 'Manage Cafes', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'color' => 'emerald'],
                        ['label' => 'View Reports', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'color' => 'accent'],
                        ['label' => 'System Settings', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z', 'color' => 'coffee'],
                    ];
                @endphp

                @foreach($quickActions as $action)
                    <a href="#" class="flex flex-col items-center justify-center p-4 rounded-xl bg-{{ $action['color'] }}-50 hover:bg-{{ $action['color'] }}-100 transition-colors duration-200 group">
                        <svg class="w-6 h-6 text-{{ $action['color'] }}-500 mb-2 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $action['icon'] }}"/>
                        </svg>
                        <span class="text-xs font-medium text-coffee-600">{{ $action['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</x-dashboard-layout>
