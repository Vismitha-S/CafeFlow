{{-- CafeFlow public navigation bar with responsive mobile menu --}}
<nav x-data="{ mobileOpen: false }" class="fixed top-0 left-0 right-0 z-50 bg-cream-50/90 backdrop-blur-md border-b border-cream-200 transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16 lg:h-20">

            {{-- Logo --}}
            <a href="/" class="shrink-0 transition-transform duration-300 hover:scale-105" aria-label="CafeFlow Home">
                <x-cafeflow-logo class="h-8 lg:h-9 w-auto" />
            </a>

            {{-- Desktop navigation links --}}
            <div class="hidden lg:flex items-center space-x-8">
                <a href="/" class="text-sm font-medium text-coffee-700 hover:text-accent-500 transition-colors duration-200">Home</a>
                <a href="#featured-cafes" class="text-sm font-medium text-coffee-700 hover:text-accent-500 transition-colors duration-200">Explore Cafes</a>
                <a href="#how-it-works" class="text-sm font-medium text-coffee-700 hover:text-accent-500 transition-colors duration-200">How It Works</a>
                <a href="#for-owners" class="text-sm font-medium text-coffee-700 hover:text-accent-500 transition-colors duration-200">About</a>
            </div>

            {{-- Desktop auth buttons --}}
            <div class="hidden lg:flex items-center space-x-4">
                @auth
                    <a href="{{ route('dashboard') }}" class="text-sm font-medium text-coffee-700 hover:text-accent-500 transition-colors duration-200">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn-primary text-sm !py-2.5 !px-5">Login</a>
                @endauth
            </div>

            {{-- Mobile menu button --}}
            <button @click="mobileOpen = !mobileOpen"
                    class="lg:hidden p-2 rounded-lg text-coffee-600 hover:bg-cream-200 transition-colors duration-200"
                    aria-label="Toggle navigation menu"
                    :aria-expanded="mobileOpen">
                <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg x-show="mobileOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile navigation menu --}}
    <div x-show="mobileOpen"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="lg:hidden bg-cream-50 border-b border-cream-200 shadow-lg">
        <div class="px-4 py-4 space-y-2">
            <a href="/" class="block px-4 py-2.5 rounded-xl text-sm font-medium text-coffee-700 hover:bg-cream-200 transition-colors duration-200">Home</a>
            <a href="#featured-cafes" @click="mobileOpen = false" class="block px-4 py-2.5 rounded-xl text-sm font-medium text-coffee-700 hover:bg-cream-200 transition-colors duration-200">Explore Cafes</a>
            <a href="#how-it-works" @click="mobileOpen = false" class="block px-4 py-2.5 rounded-xl text-sm font-medium text-coffee-700 hover:bg-cream-200 transition-colors duration-200">How It Works</a>
            <a href="#for-owners" @click="mobileOpen = false" class="block px-4 py-2.5 rounded-xl text-sm font-medium text-coffee-700 hover:bg-cream-200 transition-colors duration-200">About</a>

            <div class="pt-3 border-t border-cream-200 space-y-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="block px-4 py-2.5 rounded-xl text-sm font-medium text-accent-600 hover:bg-accent-50 transition-colors duration-200">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="block px-4 py-2.5 rounded-xl text-sm font-medium text-white bg-accent-500 hover:bg-accent-600 text-center transition-colors duration-200">Login</a>
                @endauth
            </div>
        </div>
    </div>
</nav>
