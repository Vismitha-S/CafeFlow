{{-- Premium Reservation Checkout screen matching CafeFlow design reference --}}
<x-dashboard-layout dashboard-role="customer">
    <x-slot name="title">Reservation Checkout</x-slot>

    <div class="max-w-5xl mx-auto space-y-6" x-data="{
        confirmed: false,
        submitting: false,
        paymentMethod: 'card',

        confirmBooking() {
            this.submitting = true;
            setTimeout(() => {
                this.submitting = false;
                this.confirmed = true;
            }, 1000);
        }
    }">

        {{-- Top Back Navigation --}}
        <div>
            <a href="{{ route('customer.cafe.show', $cafe['slug']) }}"
               class="inline-flex items-center gap-2 text-xs font-semibold text-coffee-600 hover:text-accent-600 transition-colors">
                <div class="w-8 h-8 rounded-xl bg-white border border-cream-200 flex items-center justify-center shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </div>
                <span>Back to Cafe</span>
            </a>
        </div>

        {{-- Main Checkout Layout (Panel 4 in reference) --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            {{-- Left Column: Selected Table & Photos (5 cols) --}}
            <div class="lg:col-span-6 space-y-5">
                <div class="dashboard-card p-6 bg-white border-cream-200 shadow-card">
                    {{-- Table header --}}
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2.5">
                            <h2 class="font-serif text-2xl font-bold text-coffee-950">{{ $selectedTable['name'] }}</h2>
                            <span class="badge bg-cream-100 text-coffee-800 border border-cream-200/90 text-[11px] font-semibold">
                                {{ $selectedTable['type'] }}
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 text-xs text-coffee-600 mb-4">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-coffee-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span>{{ $selectedTable['capacity'] }}</span>
                        </span>
                        <span>•</span>
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-coffee-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            <span>{{ $selectedTable['location'] }}</span>
                        </span>
                    </div>

                    {{-- Main Table Image --}}
                    <div class="aspect-4/3 rounded-2xl overflow-hidden bg-cream-100 border border-cream-200 mb-3 shadow-xs">
                        <img src="{{ $selectedTable['image'] }}" alt="{{ $selectedTable['name'] }}" class="w-full h-full object-cover">
                    </div>

                    {{-- Gallery Thumbnails --}}
                    <div class="grid grid-cols-3 gap-2.5">
                        @foreach(array_slice($cafe['gallery'], 0, 3) as $thumb)
                            <div class="aspect-4/3 rounded-xl overflow-hidden bg-cream-100 border border-cream-100">
                                <img src="{{ $thumb }}" alt="Photo" class="w-full h-full object-cover">
                            </div>
                        @endforeach
                    </div>

                    <p class="text-xs text-coffee-500 mt-4 leading-relaxed font-sans">
                        {{ $selectedTable['description'] ?? 'Curated seating designed for comfortable dining, conversation, and quiet leisure.' }}
                    </p>
                </div>
            </div>

            {{-- Right Column: Reservation Details & Payment Summary (6 cols) --}}
            <div class="lg:col-span-6 space-y-6">
                <div class="dashboard-card p-6 bg-[#FAF7F2] border-cream-300 shadow-card space-y-6">

                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-accent-600">Booking Summary</span>
                        <h3 class="font-serif text-2xl font-bold text-coffee-950 tracking-tight mt-0.5">Reservation Details</h3>
                        <p class="text-xs text-coffee-600 mt-0.5">{{ $cafe['name'] }} • {{ $cafe['location'] }}</p>
                    </div>

                    {{-- Selected Parameters (Date, Time, Guests) --}}
                    <div class="space-y-3 pb-5 border-b border-cream-200 text-xs">
                        <div class="flex items-center justify-between p-3 rounded-xl bg-white border border-cream-200">
                            <span class="text-coffee-500 font-medium flex items-center gap-2">
                                <svg class="w-4 h-4 text-coffee-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Date
                            </span>
                            <span class="font-semibold text-coffee-900">{{ $date }}</span>
                        </div>

                        <div class="flex items-center justify-between p-3 rounded-xl bg-white border border-cream-200">
                            <span class="text-coffee-500 font-medium flex items-center gap-2">
                                <svg class="w-4 h-4 text-coffee-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Time
                            </span>
                            <span class="font-semibold text-coffee-900">{{ $time }}</span>
                        </div>

                        <div class="flex items-center justify-between p-3 rounded-xl bg-white border border-cream-200">
                            <span class="text-coffee-500 font-medium flex items-center gap-2">
                                <svg class="w-4 h-4 text-coffee-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Guests
                            </span>
                            <span class="font-semibold text-coffee-900">{{ $guests }}</span>
                        </div>
                    </div>

                    {{-- Dynamic Reservation Fee Card --}}
                    <div class="p-4 rounded-2xl bg-amber-50/60 border border-amber-200/70 space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-coffee-900 flex items-center gap-1.5">
                                <span>Reservation Fee</span>
                                <svg class="w-3.5 h-3.5 text-coffee-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                            <span class="font-serif text-base font-bold text-accent-600">
                                LKR {{ number_format($reservationFee) }}
                            </span>
                        </div>
                        <p class="text-[11px] text-coffee-600 leading-relaxed font-sans">
                            The reservation fee will be credited toward your final bill when you visit.
                        </p>
                    </div>

                    {{-- Financial Breakdown --}}
                    <div class="space-y-2.5 text-xs pb-4 border-b border-cream-200">
                        <div class="flex items-center justify-between text-coffee-600">
                            <span>Subtotal</span>
                            <span class="font-medium text-coffee-800">LKR {{ number_format($reservationFee) }}</span>
                        </div>

                        <div class="flex items-center justify-between text-coffee-600">
                            <span>Platform Fee</span>
                            <span class="font-medium text-sage-700">LKR 0 (Free)</span>
                        </div>

                        <div class="flex items-center justify-between text-coffee-600">
                            <span>Amount Paid Now</span>
                            <span class="font-medium text-coffee-900">LKR {{ number_format($reservationFee) }}</span>
                        </div>

                        <div class="flex items-center justify-between text-coffee-600">
                            <span>Remaining Balance</span>
                            <span class="font-medium text-coffee-800">LKR 0</span>
                        </div>

                        <div class="flex items-center justify-between pt-2 border-t border-cream-200 text-sm font-bold text-coffee-950">
                            <span>Total to Pay</span>
                            <span class="font-serif text-lg text-accent-600">LKR {{ number_format($totalAmount) }}</span>
                        </div>
                    </div>

                    {{-- Dynamic Cancellation Policy Section --}}
                    <div class="p-3.5 rounded-2xl bg-sage-50/70 border border-sage-200/60 text-xs text-sage-900 space-y-1">
                        <div class="flex items-center gap-1.5 font-bold text-sage-800">
                            <svg class="w-4 h-4 text-sage-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <span>Dynamic Cancellation Policy</span>
                        </div>
                        <p class="text-[11px] text-sage-700 leading-relaxed font-sans">
                            {{ $cafe['cancellation_policy'] }}
                        </p>
                    </div>

                    {{-- CTA Confirm Button --}}
                    <div class="space-y-3">
                        <button @click="confirmBooking()"
                                :disabled="submitting"
                                class="btn-primary w-full py-3.5 text-sm font-semibold tracking-tight shadow-card transition-all">
                            <span x-show="!submitting">Confirm Reservation</span>
                            <span x-show="submitting" class="inline-flex items-center gap-2">
                                <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Processing Reservation...
                            </span>
                        </button>

                        <p class="text-[11px] text-center text-coffee-400">
                            Instant booking confirmation • Guaranteed table hold for 20 minutes
                        </p>
                    </div>

                </div>
            </div>
        </div>

        {{-- Success Modal Simulation --}}
        <div x-show="confirmed"
             x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-coffee-950/60 backdrop-blur-sm">
            <div class="bg-white rounded-3xl p-8 max-w-md w-full border border-cream-200 shadow-2xl text-center space-y-4">
                <div class="w-16 h-16 rounded-full bg-sage-100 text-sage-700 mx-auto flex items-center justify-center">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h3 class="font-serif text-2xl font-bold text-coffee-950">Reservation Confirmed!</h3>
                <p class="text-xs text-coffee-600 leading-relaxed font-sans">
                    Your table at <strong class="text-coffee-900">{{ $cafe['name'] }}</strong> is confirmed for {{ $date }} at {{ $time }}. We look forward to hosting you!
                </p>
                <div class="p-3 bg-cream-50 rounded-xl text-xs text-coffee-700 border border-cream-200">
                    Booking Code: <strong class="font-mono text-coffee-900">RES-2026-{{ rand(100, 999) }}</strong>
                </div>
                <div class="pt-2">
                    <a href="{{ route('customer.reservations') }}" class="btn-primary w-full py-2.5 text-xs font-semibold">
                        View My Reservations
                    </a>
                </div>
            </div>
        </div>

    </div>
</x-dashboard-layout>
