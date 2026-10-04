<div class="min-h-screen flex flex-col justify-center items-center py-12 px-4 sm:px-6 lg:px-8 bg-[#FAF7F2] relative overflow-hidden">
    {{-- Subtle decorative ambient background glows --}}
    <div class="absolute -top-32 -left-32 w-96 h-96 rounded-full bg-cream-200/50 blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 rounded-full bg-accent-100/30 blur-3xl pointer-events-none"></div>

    <div class="mb-6 z-10 flex flex-col items-center">
        {{ $logo }}
    </div>

    <div class="w-full sm:max-w-md bg-white/95 backdrop-blur-md border border-cream-200/90 rounded-3xl p-6 sm:p-8 shadow-card relative z-10">
        {{ $slot }}
    </div>

    {{-- Subtle footer copyright --}}
    <div class="mt-8 text-center text-xs text-coffee-400 z-10 font-medium">
        &copy; {{ date('Y') }} CafeFlow. Artisanal Cafe Reservations.
    </div>
</div>
