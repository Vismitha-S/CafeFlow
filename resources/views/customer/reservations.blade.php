{{-- Customer Reservations page matching CafeFlow design reference --}}
<x-dashboard-layout dashboard-role="customer">
    <x-slot name="title">My Reservations</x-slot>

    <div class="space-y-10" x-data="{
        activeTab: 'upcoming',
        cancelModalOpen: false,
        detailsModalOpen: false,
        selectedReservation: null,
        cancelConfirmed: false,
        cancelling: false,
        cancelError: '',

        openDetailsModal(reservation) {
            this.selectedReservation = reservation;
            this.detailsModalOpen = true;
        },
        openCancelModal(reservation) {
            this.selectedReservation = reservation;
            this.detailsModalOpen = false;
            this.cancelModalOpen = true;
            this.cancelConfirmed = false;
            this.cancelling = false;
            this.cancelError = '';
        },
        async executeCancellation() {
            if (!this.selectedReservation || this.cancelling) return;
            this.cancelling = true;
            this.cancelError = '';

            try {
                if (this.selectedReservation.cancel_url) {
                    const token = document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || '{{ csrf_token() }}';
                    const response = await fetch(this.selectedReservation.cancel_url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': token
                        },
                        body: JSON.stringify({
                            cancellation_reason: 'Customer requested cancellation'
                        })
                    });

                    if (!response.ok) {
                        const data = await response.json();
                        throw new Error(data.message || 'Unable to process cancellation.');
                    }
                }

                this.selectedReservation.status = 'cancelled';
                this.selectedReservation.status_label = 'Cancelled';
                this.selectedReservation.cancellation_allowed = false;
                this.cancelConfirmed = true;
                setTimeout(() => {
                    this.cancelModalOpen = false;
                    this.cancelling = false;
                    if (this.selectedReservation.cancel_url) {
                        window.location.reload();
                    }
                }, 2500);
            } catch (err) {
                this.cancelError = err.message || 'An error occurred while cancelling the reservation.';
                this.cancelling = false;
            }
        }
    }">

        {{-- Page Header --}}
        <div class="flex items-center justify-between pb-3 border-b border-cream-200">
            <div>
                <h1 class="font-serif text-3xl sm:text-4xl font-bold text-coffee-950 tracking-tight">My Reservations</h1>
                <p class="text-xs sm:text-sm text-coffee-500 mt-1">Manage and track your cafe table bookings.</p>
            </div>

            <a href="{{ route('customer.explore') }}" class="btn-primary py-2 px-4 text-xs font-semibold">
                <span>Book New Table</span>
            </a>
        </div>

        {{-- Tabs: Upcoming, Past, Cancelled (Panel 6 in reference) --}}
        <div class="flex items-center gap-2 text-xs">
            <button @click="activeTab = 'upcoming'"
                    class="px-5 py-2.5 rounded-full font-semibold transition-all duration-200"
                    :class="activeTab === 'upcoming' ? 'bg-coffee-900 text-cream-50 shadow-xs' : 'bg-white hover:bg-cream-100 text-coffee-700 border border-cream-200'">
                Upcoming
            </button>
            <button @click="activeTab = 'past'"
                    class="px-5 py-2.5 rounded-full font-semibold transition-all duration-200"
                    :class="activeTab === 'past' ? 'bg-coffee-900 text-cream-50 shadow-xs' : 'bg-white hover:bg-cream-100 text-coffee-700 border border-cream-200'">
                Past
            </button>
            <button @click="activeTab = 'cancelled'"
                    class="px-5 py-2.5 rounded-full font-semibold transition-all duration-200"
                    :class="activeTab === 'cancelled' ? 'bg-coffee-900 text-cream-50 shadow-xs' : 'bg-white hover:bg-cream-100 text-coffee-700 border border-cream-200'">
                Cancelled
            </button>
        </div>

        {{-- Reservations List --}}
        <div class="space-y-4">
            @foreach($reservations as $res)
                @php
                    $tabCategory = in_array($res['status'], ['confirmed', 'pending']) ? 'upcoming' : ($res['status'] === 'completed' ? 'past' : 'cancelled');
                @endphp

                <div x-show="activeTab === '{{ $tabCategory }}'"
                     class="dashboard-card p-4 sm:p-5 flex flex-col md:flex-row md:items-center justify-between gap-4 hover:border-cream-300 transition-all duration-200">

                    {{-- Left side: Cafe Image & Details --}}
                    <div class="flex items-center gap-4 min-w-0">
                        <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl overflow-hidden bg-cream-100 shrink-0 border border-cream-200">
                            <img src="{{ $res['cafe_image'] }}" alt="{{ $res['cafe_name'] }}" class="w-full h-full object-cover">
                        </div>

                        <div class="min-w-0 space-y-1">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('customer.cafe.show', $res['cafe_slug']) }}" class="font-serif text-lg font-bold text-coffee-950 hover:text-accent-600 transition-colors truncate">
                                    {{ $res['cafe_name'] }}
                                </a>
                                <span class="text-xs text-coffee-400 font-normal">({{ $res['cafe_location'] }})</span>
                            </div>

                            <p class="text-xs font-semibold text-coffee-800 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-accent-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span>{{ $res['date'] }} • {{ $res['time'] }}</span>
                            </p>

                            <p class="text-xs text-coffee-500 flex items-center gap-1.5">
                                <span>{{ $res['table_name'] }} ({{ $res['table_type'] }})</span>
                                <span>•</span>
                                <span>{{ $res['guests'] }} Guests</span>
                                <span>•</span>
                                <span class="font-medium text-coffee-700">Fee: LKR {{ number_format($res['reservation_fee'], 2) }}</span>
                            </p>
                        </div>
                    </div>

                    {{-- Right side: Status Badge & Actions --}}
                    <div class="flex sm:flex-col md:items-end justify-between items-center gap-3 pt-3 md:pt-0 border-t md:border-t-0 border-cream-100">
                        {{-- Status Pill --}}
                        <div>
                            @if($res['status'] === 'confirmed')
                                <span class="badge-sage text-xs font-semibold">
                                    <span class="w-2 h-2 rounded-full bg-sage-500 animate-pulse"></span>
                                    <span>Confirmed</span>
                                </span>
                            @elseif($res['status'] === 'pending')
                                <span class="badge bg-amber-50 text-amber-700 border border-amber-200/80 text-xs font-semibold">
                                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                    <span>Pending</span>
                                </span>
                            @elseif($res['status'] === 'completed')
                                <span class="badge bg-cream-100 text-coffee-700 border border-cream-200 text-xs font-semibold">
                                    <span>Completed</span>
                                </span>
                            @else
                                <span class="badge bg-rose-50 text-rose-700 border border-rose-200/80 text-xs font-semibold">
                                    <span>Cancelled</span>
                                </span>
                            @endif
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex items-center gap-2">
                            <button type="button"
                                    @click="openDetailsModal({{ json_encode($res) }})"
                                    class="btn-secondary px-3 py-1.5 text-xs font-semibold">
                                View Details
                            </button>

                            @if($res['cancellation_allowed'] ?? false)
                                <button @click="openCancelModal({{ json_encode($res) }})"
                                        class="px-3 py-1.5 text-xs font-semibold text-rose-600 hover:text-rose-800 hover:bg-rose-50 rounded-xl transition-colors">
                                    Cancel
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- My Favourites Carousel / Section at bottom (Panel 6 in reference) --}}
        <div class="pt-8 border-t border-cream-200 space-y-4">
            <div class="flex items-end justify-between">
                <div>
                    <h2 class="font-serif text-2xl font-bold text-coffee-950 tracking-tight">My Favourites</h2>
                    <p class="text-xs text-coffee-500 mt-0.5">Places you have saved for cozy coffee mornings.</p>
                </div>
                <a href="{{ route('customer.favourites') }}" class="text-xs font-semibold text-accent-600 hover:text-accent-700">
                    View All
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @foreach(array_slice($favourites, 0, 4) as $fav)
                    <div class="bg-white rounded-2xl border border-cream-200 p-3 shadow-subtle hover:shadow-card hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between">
                        <div>
                            <div class="relative aspect-4/3 rounded-xl overflow-hidden mb-2.5">
                                <img src="{{ $fav['image'] }}" alt="{{ $fav['name'] }}" class="w-full h-full object-cover">
                                <span class="absolute top-2 right-2 w-7 h-7 rounded-full bg-white/90 text-rose-500 flex items-center justify-center shadow-xs">
                                    <svg class="w-3.5 h-3.5 fill-rose-500" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                </span>
                            </div>
                            <h4 class="font-serif text-xs font-bold text-coffee-900 truncate">{{ $fav['name'] }}</h4>
                            <p class="text-[11px] text-coffee-500 truncate">{{ $fav['location'] }}</p>
                        </div>
                        <div class="pt-2 border-t border-cream-100 flex items-center justify-between mt-2">
                            <span class="text-[11px] font-semibold text-coffee-800">★ {{ $fav['rating'] }}</span>
                            <a href="{{ route('customer.cafe.show', $fav['slug']) }}" class="text-[11px] font-semibold text-accent-600">Book &rarr;</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Reservation Details Modal --}}
        <div x-show="detailsModalOpen"
             x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-coffee-950/60 backdrop-blur-sm">
            <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full border border-cream-200 shadow-2xl space-y-5 relative">
                <button type="button" @click="detailsModalOpen = false" class="absolute top-5 right-5 p-2 rounded-full text-coffee-400 hover:text-coffee-700 hover:bg-cream-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>

                <div class="flex items-center gap-4 pr-8">
                    <div class="w-16 h-16 rounded-2xl overflow-hidden bg-cream-100 shrink-0 border border-cream-200">
                        <img :src="selectedReservation ? selectedReservation.cafe_image : ''" :alt="selectedReservation ? selectedReservation.cafe_name : ''" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <h3 class="font-serif text-xl font-bold text-coffee-950" x-text="selectedReservation ? selectedReservation.cafe_name : ''"></h3>
                        <p class="text-xs text-coffee-500" x-text="selectedReservation ? selectedReservation.cafe_location : ''"></p>
                        <p class="text-[11px] text-accent-600 font-semibold mt-0.5" x-text="selectedReservation ? (selectedReservation.reference || ('#RES-' + selectedReservation.id)) : ''"></p>
                    </div>
                </div>

                {{-- Status & Summary Grid --}}
                <div class="grid grid-cols-2 gap-3 p-4 bg-cream-50/80 rounded-2xl border border-cream-200 text-xs">
                    <div>
                        <span class="text-coffee-400 text-[11px] block">Status</span>
                        <span class="font-semibold text-coffee-900 capitalize" x-text="selectedReservation ? selectedReservation.status_label : ''"></span>
                    </div>
                    <div>
                        <span class="text-coffee-400 text-[11px] block">Payment Status</span>
                        <span class="font-semibold text-coffee-900" x-text="selectedReservation ? selectedReservation.payment_status : ''"></span>
                    </div>
                    <div>
                        <span class="text-coffee-400 text-[11px] block">Date & Time</span>
                        <span class="font-semibold text-coffee-900" x-text="selectedReservation ? (selectedReservation.date + ' • ' + selectedReservation.time) : ''"></span>
                    </div>
                    <div>
                        <span class="text-coffee-400 text-[11px] block">Table & Guests</span>
                        <span class="font-semibold text-coffee-900" x-text="selectedReservation ? (selectedReservation.table_name + ' (' + selectedReservation.guests + ' Guests)') : ''"></span>
                    </div>
                    <div>
                        <span class="text-coffee-400 text-[11px] block">Reservation Fee</span>
                        <span class="font-semibold text-accent-700" x-text="selectedReservation ? ('LKR ' + Number(selectedReservation.reservation_fee).toFixed(2)) : ''"></span>
                    </div>
                    <div>
                        <span class="text-coffee-400 text-[11px] block">Paid Amount</span>
                        <span class="font-semibold text-coffee-900" x-text="selectedReservation ? ('LKR ' + Number(selectedReservation.paid_amount || selectedReservation.reservation_fee).toFixed(2)) : ''"></span>
                    </div>
                </div>

                {{-- Cancellation Policy Note --}}
                <div class="p-3.5 bg-cream-100/60 rounded-2xl border border-cream-200 text-xs space-y-1">
                    <h4 class="font-bold text-coffee-800">Cancellation Policy:</h4>
                    <p class="text-[11px] text-coffee-600 leading-relaxed">
                        A 50% cancellation fee will be deducted upon cancellation. The remaining 50% is refunded to your payment method.
                    </p>
                </div>

                {{-- Modal Actions --}}
                <div class="flex items-center justify-between pt-2">
                    <a :href="selectedReservation ? ('/cafes/' + selectedReservation.cafe_slug) : '#'"
                       class="text-xs font-semibold text-accent-600 hover:text-accent-700 flex items-center gap-1">
                        <span>Visit Cafe Page</span>
                        <span>&rarr;</span>
                    </a>

                    <div class="flex items-center gap-2">
                        <template x-if="selectedReservation && selectedReservation.cancellation_allowed">
                            <button @click="openCancelModal(selectedReservation)"
                                    class="px-3.5 py-2 bg-rose-50 text-rose-700 hover:bg-rose-100 rounded-xl text-xs font-semibold transition-colors">
                                Cancel Reservation
                            </button>
                        </template>
                        <button @click="detailsModalOpen = false" class="btn-primary py-2 px-4 text-xs font-semibold">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Dynamic Cancellation Policy Modal --}}
        <div x-show="cancelModalOpen"
             x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-coffee-950/60 backdrop-blur-sm">
            <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full border border-cream-200 shadow-2xl space-y-4">
                <div class="flex items-center gap-3 text-rose-600">
                    <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <h3 class="font-serif text-xl font-bold text-coffee-950">Cancel Reservation?</h3>
                </div>

                <div x-show="!cancelConfirmed" class="space-y-4">
                    <p class="text-xs text-coffee-600 leading-relaxed font-sans">
                        Are you sure you want to cancel your table at <strong class="text-coffee-900" x-text="selectedReservation ? selectedReservation.cafe_name : ''"></strong>?
                    </p>

                    {{-- Deduction and Refund Breakdown Box --}}
                    <div class="p-4 bg-cream-50 rounded-2xl border border-cream-200 text-xs space-y-2.5">
                        <h4 class="font-bold text-coffee-800">Cancellation Fee & Refund Summary:</h4>

                        <div class="space-y-1.5 text-[11px] pt-1">
                            <div class="flex items-center justify-between text-coffee-600">
                                <span>Total Booking Fee Paid:</span>
                                <span class="font-medium text-coffee-900" x-text="selectedReservation ? ('LKR ' + Number(selectedReservation.reservation_fee).toFixed(2)) : 'LKR 0.00'"></span>
                            </div>
                            <div class="flex items-center justify-between text-rose-700 font-medium">
                                <span>Deduction Fee (50%):</span>
                                <span x-text="selectedReservation ? ('- LKR ' + (Number(selectedReservation.reservation_fee) * 0.5).toFixed(2)) : '- LKR 0.00'"></span>
                            </div>
                            <div class="flex items-center justify-between text-sage-800 font-bold pt-1.5 border-t border-cream-200">
                                <span>Refund to Payment Method (50%):</span>
                                <span class="text-sage-700" x-text="selectedReservation ? ('LKR ' + (Number(selectedReservation.reservation_fee) * 0.5).toFixed(2)) : 'LKR 0.00'"></span>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-cream-200/80 text-[11px] text-coffee-500 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-accent-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Upon cancellation, your table slot will immediately be released and available for others to book.</span>
                        </div>
                    </div>

                    <div x-show="cancelError" class="p-3 bg-rose-50 text-rose-700 rounded-xl text-xs" x-text="cancelError"></div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button :disabled="cancelling" @click="cancelModalOpen = false" class="btn-secondary py-2 px-4 text-xs font-semibold disabled:opacity-50">
                            Keep Reservation
                        </button>
                        <button :disabled="cancelling" @click="executeCancellation()" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-semibold transition-colors disabled:opacity-50 flex items-center gap-1.5">
                            <span x-show="!cancelling">Confirm Cancellation</span>
                            <span x-show="cancelling">Processing...</span>
                        </button>
                    </div>
                </div>

                {{-- Cancellation Confirmation Success with Deduction and Refund --}}
                <div x-show="cancelConfirmed" class="text-center py-4 space-y-3">
                    <div class="w-12 h-12 rounded-full bg-sage-100 text-sage-700 mx-auto flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <p class="text-base font-serif font-bold text-coffee-950">Reservation Cancelled</p>

                    <div class="p-3.5 bg-cream-50 rounded-2xl border border-cream-200 text-xs space-y-1.5 text-left max-w-sm mx-auto">
                        <div class="flex items-center justify-between text-coffee-600 text-[11px]">
                            <span>Deduction (50% fee):</span>
                            <span class="font-semibold text-rose-700" x-text="selectedReservation ? ('- LKR ' + (Number(selectedReservation.reservation_fee) * 0.5).toFixed(2)) : ''"></span>
                        </div>
                        <div class="flex items-center justify-between text-coffee-600 text-[11px]">
                            <span>Refund Amount (50%):</span>
                            <span class="font-semibold text-sage-700" x-text="selectedReservation ? ('LKR ' + (Number(selectedReservation.reservation_fee) * 0.5).toFixed(2)) : ''"></span>
                        </div>
                    </div>

                    <p class="text-xs text-coffee-600 px-2 leading-relaxed">
                        A 50% cancellation fee was deducted. Your remaining 50% refund has been processed. The table slot is now available back for booking.
                    </p>

                    <div class="pt-2">
                        <button @click="cancelModalOpen = false; if (selectedReservation && selectedReservation.cancel_url) { window.location.reload(); }" class="btn-primary py-2 px-6 text-xs font-semibold">
                            Done
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-dashboard-layout>
