<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'CafeFlow') }}</title>

        <!-- Google / Bunny Fonts for vintage cafe aesthetic -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=playfair-display:400,500,600,700,800|plus-jakarta-sans:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts and Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="h-full font-sans antialiased bg-[#FAF7F2] text-coffee-800 selection:bg-accent-100 selection:text-coffee-900">
        <div class="min-h-full">
            {{ $slot }}
        </div>

        @livewireScripts
    </body>
</html>
