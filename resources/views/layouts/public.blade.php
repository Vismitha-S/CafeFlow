{{-- Public-facing layout for guest pages (landing, explore, etc.) --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="{{ $metaDescription ?? 'CafeFlow - Discover, reserve, and enjoy the best cafes near you. Easy table reservations with deposit protection.' }}">

        <title>{{ $title ?? 'CafeFlow - Your Cafe Reservation Platform' }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&family=playfair-display:400,700&display=swap" rel="stylesheet" />

        <!-- Scripts and Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>
    <body class="font-sans antialiased bg-cream-50 text-coffee-800">
        {{-- Public navigation bar --}}
        @include('partials.public-navbar')

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>

        {{-- Site footer --}}
        @include('partials.footer')

        @livewireScripts
    </body>
</html>
