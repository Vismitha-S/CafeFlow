{{-- CafeFlow dashboard layout with sidebar navigation --}}
{{-- This layout is shared by admin, owner, and customer dashboards --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Dashboard' }} - CafeFlow</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&family=playfair-display:400,700&display=swap" rel="stylesheet" />

        <!-- Scripts and Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>
    <body class="font-sans antialiased bg-cream-50 text-coffee-800">
        <x-banner />

        <div x-data="{ sidebarOpen: false }" class="min-h-screen flex">

            {{-- Sidebar navigation --}}
            @include('partials.dashboard-sidebar')

            {{-- Main content area --}}
            <div class="flex-1 flex flex-col min-w-0">

                {{-- Top bar --}}
                <header class="sticky top-0 z-30 bg-white/80 backdrop-blur-md border-b border-cream-200">
                    <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                        {{-- Mobile sidebar toggle --}}
                        <button @click="sidebarOpen = true"
                                class="lg:hidden p-2 rounded-lg text-coffee-500 hover:bg-cream-100 transition-colors duration-200"
                                aria-label="Open sidebar">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>

                        {{-- Page title --}}
                        <h1 class="text-lg font-semibold text-coffee-800 hidden lg:block">
                            {{ $header ?? 'Dashboard' }}
                        </h1>

                        {{-- User dropdown --}}
                        <div class="flex items-center gap-4">
                            {{-- Notification bell placeholder --}}
                            <button class="p-2 rounded-lg text-coffee-400 hover:bg-cream-100 transition-colors duration-200 relative" aria-label="Notifications">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-accent-500 rounded-full"></span>
                            </button>

                            {{-- User avatar and dropdown --}}
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="flex items-center gap-3 text-sm rounded-lg p-1.5 hover:bg-cream-100 transition-colors duration-200">
                                        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                            <img class="w-8 h-8 rounded-full object-cover ring-2 ring-cream-200"
                                                 src="{{ Auth::user()->profile_photo_url }}"
                                                 alt="{{ Auth::user()->name }}">
                                        @endif
                                        <span class="hidden sm:block font-medium text-coffee-700">{{ Auth::user()->name }}</span>
                                        <svg class="w-4 h-4 text-coffee-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <div class="block px-4 py-2 text-xs text-coffee-400">Manage Account</div>
                                    <x-dropdown-link href="{{ route('profile.show') }}">Profile</x-dropdown-link>

                                    @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                        <x-dropdown-link href="{{ route('api-tokens.index') }}">API Tokens</x-dropdown-link>
                                    @endif

                                    <div class="border-t border-cream-100"></div>

                                    <form method="POST" action="{{ route('logout') }}" x-data>
                                        @csrf
                                        <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                                            Log Out
                                        </x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    </div>
                </header>

                {{-- Main page content --}}
                <main class="flex-1 p-4 sm:p-6 lg:p-8">
                    {{ $slot }}
                </main>
            </div>
        </div>

        @stack('modals')
        @livewireScripts
    </body>
</html>
