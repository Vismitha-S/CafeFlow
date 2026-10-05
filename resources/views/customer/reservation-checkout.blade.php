{{-- Reservation checkout with a server-confirmed demo card payment step --}}
<x-dashboard-layout dashboard-role="customer">
    <x-slot name="title">Reservation Checkout</x-slot>
    <div class="max-w-5xl mx-auto space-y-6" x-data="{
        paymentFormOpen: @js($showPaymentForm),
        confirmed: @js($paymentConfirmed),
        submitting: false,
    }">
        <div>
            <a href="{{ route('customer.cafe.show', $cafe['slug']) }}"
               class="inline-flex items-center gap-2 text-xs font-semibold text-coffee-600 hover:text-accent-600 transition-colors">
                <span class="w-8 h-8 rounded-xl bg-white border border-cream-200 flex items-center justify-center shadow-xs" aria-hidden="true">←</span>
                <span>Back to Cafe</span>
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
            <div class="md:col-span-1">
                <div class="dashboard-card p-6 bg-white border-cream-200 shadow-card">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2.5">
                            <h2 class="font-serif text-2xl font-bold text-coffee-950">{{ $selectedTable['name'] }}</h2>
                            <span class="badge bg-cream-100 text-coffee-800 border border-cream-200/90 text-[11px] font-semibold">{{ $selectedTable['type'] }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 text-xs text-coffee-600 mb-4">
                        <span>{{ $selectedTable['capacity'] }} guests</span>
                        <span aria-hidden="true">•</span>
                        <span>{{ $selectedTable['location'] }}</span>
                    </div>
                    <div class="aspect-video rounded-2xl overflow-hidden bg-cream-100 border border-cream-200 mb-3 shadow-xs">
                        @if(!empty($selectedTable['image']))
                            <img src="{{ $selectedTable['image'] }}" alt="{{ $selectedTable['name'] ?? 'Table' }}" class="w-full h-full object-cover">
                        @elseif(!empty($cafe['image']))
                            <img src="{{ $cafe['image'] }}" alt="{{ $cafe['name'] ?? 'Cafe' }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center text-coffee-400 bg-cream-50">
                                <svg class="w-10 h-10 mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                        @endif
                    </div>
                    <div class="grid grid-cols-3 gap-2.5">
                        @foreach(array_slice($cafe['gallery'] ?? [], 0, 3) as $thumb)
                            <div class="aspect-square rounded-xl overflow-hidden bg-cream-100 border border-cream-100">
                                <img src="{{ $thumb }}" alt="{{ $cafe['name'] }}" class="w-full h-full object-cover">
                            </div>
                        @endforeach
                    </div>
                    @if(!empty($selectedTable['description']))
                        <p class="text-xs text-coffee-500 mt-4 leading-relaxed font-sans">{{ $selectedTable['description'] }}</p>
                    @endif
                </div>
            </div>
            <div class="md:col-span-1">
                <div class="dashboard-card p-6 bg-[#FAF7F2] border-cream-300 shadow-card space-y-5">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-accent-600">Booking Summary</span>
                        <h3 class="font-serif text-2xl font-bold text-coffee-950 mt-0.5">Reservation Details</h3>
                        <p class="text-xs text-coffee-600 mt-0.5">{{ $cafe['name'] }} • {{ $cafe['location'] }}</p>
                    </div>
                    <div class="space-y-3 pb-5 border-b border-cream-200 text-xs">
                        <div class="flex items-center justify-between p-3 rounded-xl bg-white border border-cream-200">
                            <span class="text-coffee-500 font-medium">Date</span>
                            <span class="font-semibold text-coffee-900">{{ $date }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 rounded-xl bg-white border border-cream-200">
                            <span class="text-coffee-500 font-medium">Time</span>
                            <span class="font-semibold text-coffee-900">{{ $time }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 rounded-xl bg-white border border-cream-200">
                            <span class="text-coffee-500 font-medium">Guests</span>
                            <span class="font-semibold text-coffee-900">{{ $guests }}</span>
                        </div>
                    </div>
                    <div class="p-4 rounded-2xl bg-amber-50/60 border border-amber-200/70 space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-coffee-900">Reservation Fee</span>
                            <span class="font-serif text-base font-bold text-accent-600">LKR {{ number_format((float) $reservationFee, 2) }}</span>
                        </div>
                        <p class="text-[11px] text-coffee-600 leading-relaxed font-sans">The reservation fee will be credited toward your final bill when you visit.</p>
                    </div>
                    <div class="space-y-2.5 text-xs pb-4 border-b border-cream-200">
                        <div class="flex items-center justify-between text-coffee-600">
                            <span>Amount Due Now</span>
                            <span class="font-medium text-coffee-900">LKR {{ number_format((float) ($depositPayment?->amount ?? $reservationFee), 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between text-coffee-600">
                            <span>Payment Method</span>
                            <span class="font-medium text-coffee-900">Credit / Debit Card</span>
                        </div>
                        <div class="flex items-center justify-between text-coffee-600">
                            <span>Payment Status</span>
                            <span class="font-medium text-coffee-900">{{ $paymentStatusLabel }}</span>
                        </div>
                        <div class="flex items-center justify-between text-coffee-600">
                            <span>Remaining Balance</span>
                            <span class="font-medium text-coffee-800">LKR {{ number_format((float) $remainingBalance, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t border-cream-200 text-sm font-bold text-coffee-950">
                            <span>Total to Pay</span>
                            <span class="font-serif text-lg text-accent-600">LKR {{ number_format((float) $totalToPay, 2) }}</span>
                        </div>
                    </div>
                    <div class="p-4 rounded-xl border border-accent-200 bg-white flex items-center gap-3">
                        <svg class="w-5 h-5 text-accent-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><rect x="2.5" y="4.5" width="19" height="15" rx="2" stroke-width="1.7"/><path d="M3 9h18M7 15h3" stroke-width="1.7" stroke-linecap="round"/></svg>
                        <div>
                            <p class="text-xs font-semibold text-coffee-900">Payment Method</p>
                            <p class="text-xs text-coffee-800">Credit / Debit Card</p>
                            <p class="text-[11px] text-coffee-500 mt-0.5">Pay the reservation fee securely to confirm your table.</p>
                        </div>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-sage-50/70 border border-sage-200/60 text-xs text-sage-900 space-y-1">
                        <div class="flex items-center gap-1.5 font-bold text-sage-800">
                            <span>Cancellation Policy</span>
                        </div>
                        <p class="text-[11px] text-sage-700 leading-relaxed font-sans">
                            @if($cancellationPenaltyDisplay === '0')
                                No cancellation penalty applies to the reservation deposit. Any eligible refund remains subject to payment status and cafe policy; refund timing is not guaranteed.
                            @else
                                A {{ $cancellationPenaltyDisplay }}% cancellation fee will be deducted upon cancellation. The remaining eligible amount will be refunded to your original payment method.
                            @endif
                        </p>
                    </div>
                    @if($errors->any())
                        <div class="rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs text-rose-800" role="alert">
                            {{ $errors->first() }}
                        </div>
                    @endif
                    @if($reservation && $depositPayment?->status === \App\Models\Payment::STATUS_PENDING)
                        <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-900" role="status">
                            Payment Pending. Your reservation has not been confirmed yet.
                        </div>
                        <button type="button" @click="paymentFormOpen = true" class="btn-primary w-full py-3.5 text-sm font-semibold shadow-card">
                            Continue to Payment
                        </button>
                    @elseif(!$reservation)
                        <form method="POST" action="{{ route('cafes.reservations.store', $cafe['id']) }}" @submit="submitting = true">
                            @csrf
                            <input type="hidden" name="_checkout" value="1">
                            <input type="hidden" name="cafe_table_id" value="{{ $selectedTable['id'] }}">
                            <input type="hidden" name="reservation_date" value="{{ \Carbon\Carbon::parse($date)->format('Y-m-d') }}">
                            <input type="hidden" name="start_time" value="{{ \Carbon\Carbon::parse($time)->format('H:i') }}">
                            <input type="hidden" name="guest_count" value="{{ (int) preg_replace('/\D+/', '', $guests) }}">
                            <button type="submit" :disabled="submitting" class="btn-primary w-full py-3.5 text-sm font-semibold shadow-card transition-all disabled:opacity-60">
                                <span x-show="!submitting">Proceed to Payment</span>
                                <span x-show="submitting">Preparing Secure Payment Form...</span>
                            </button>
                        </form>
                    @endif
                    @if($reservation && $depositPayment?->status === \App\Models\Payment::STATUS_PENDING)
                        <p class="text-[11px] text-center text-coffee-500">Demo payment only. No external payment gateway is connected.</p>
                    @endif
                </div>
            </div>
        </div>
        @if($reservation && $depositPayment?->status === \App\Models\Payment::STATUS_PENDING)
            <div x-show="paymentFormOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-coffee-950/60 backdrop-blur-sm" role="dialog" aria-modal="true" aria-labelledby="demo-payment-title">
                <div class="relative bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full border border-cream-200 shadow-2xl">
                    <button type="button" @click="paymentFormOpen = false" aria-label="Close payment form"
                            class="z-10 w-9 h-9 rounded-full border border-cream-200 bg-white text-coffee-600 hover:bg-cream-50 font-semibold flex items-center justify-center"
                            style="position:absolute;top:1rem;right:1rem;">&#x2715;</button>
                    <div class="pr-10 mb-6">
                        <span class="text-xs font-semibold uppercase tracking-wider text-accent-600">CafeFlow Demo</span>
                        <h2 id="demo-payment-title" class="font-serif text-2xl font-bold text-coffee-950 mt-1">Payment Details</h2>
                        <p class="text-xs text-coffee-600 mt-1">Demo payment processed within CafeFlow; no external gateway is connected.</p>
                        <p class="text-[11px] text-coffee-500 mt-1">Reservation: #{{ $reservation->id }} • {{ $cafe['name'] }}</p>
                    </div>
                    <div class="mb-5 p-4 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-between">
                        <span class="text-xs font-semibold text-coffee-800">Amount to Pay</span>
                        <span class="font-serif text-xl font-bold text-accent-700">LKR {{ number_format((float) $depositPayment->amount, 2) }}</span>
                    </div>
                    <form method="POST" action="{{ route('reservations.payments.demo-confirmation', ['reservation' => $reservation->id, 'payment' => $depositPayment->id]) }}" @submit="submitting = true">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label for="cardholder-name" class="block text-xs font-semibold text-coffee-700 mb-1.5">Cardholder Name</label>
                                <input id="cardholder-name" name="cardholder_name" value="{{ old('cardholder_name') }}" autocomplete="cc-name" required maxlength="120" class="w-full bg-white text-sm text-coffee-800 rounded-xl border border-cream-200 py-2.5 px-3 focus:ring-2 focus:ring-accent-400/20">
                                @error('cardholder_name')<p class="mt-1 text-xs text-rose-700">{{ $message }}</p>@enderror
                            </div>
                            <div x-data="{
                                raw: '',
                                get formatted() {
                                    return this.raw.replace(/\D/g, '').slice(0, 16).replace(/(\d{4})(?=\d)/g, '$1 ');
                                },
                                onInput(e) {
                                    this.raw = e.target.value.replace(/\D/g, '').slice(0, 16);
                                    this.$nextTick(() => { e.target.value = this.formatted; });
                                }
                            }">
                                <label for="card-number" class="block text-xs font-semibold text-coffee-700 mb-1.5">Card Number</label>
                                <input id="card-number" type="text" inputmode="numeric" autocomplete="cc-number"
                                       placeholder="1234 5678 9012 3456"
                                       maxlength="19"
                                       :value="formatted"
                                       @input="onInput($event)"
                                       required
                                       class="w-full bg-white text-sm text-coffee-800 rounded-xl border border-cream-200 py-2.5 px-3 focus:ring-2 focus:ring-accent-400/20 tracking-widest">
                                <input type="hidden" name="card_number" :value="raw">
                                @error('card_number')<p class="mt-1 text-xs text-rose-700">{{ $message }}</p>@enderror
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="expiry-date" class="block text-xs font-semibold text-coffee-700 mb-1.5">Expiry Date</label>
                                    <input id="expiry-date" name="expiry_date" type="text" inputmode="numeric"
                                           autocomplete="cc-exp" placeholder="MM/YY" maxlength="5" required
                                           x-on:input="let v=$event.target.value.replace(/[^0-9]/g,``);if(v.length>=3){v=v.slice(0,2)+`/`+v.slice(2,4);}$event.target.value=v;"
                                           class="w-full bg-white text-sm text-coffee-800 rounded-xl border border-cream-200 py-2.5 px-3 focus:ring-2 focus:ring-accent-400/20">
                                    @error('expiry_date')<p class="mt-1 text-xs text-rose-700">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="cvv" class="block text-xs font-semibold text-coffee-700 mb-1.5">CVV</label>
                                    <input id="cvv" name="cvv" type="password" inputmode="numeric" autocomplete="cc-csc" required maxlength="4" class="w-full bg-white text-sm text-coffee-800 rounded-xl border border-cream-200 py-2.5 px-3 focus:ring-2 focus:ring-accent-400/20">
                                    @error('cvv')<p class="mt-1 text-xs text-rose-700">{{ $message }}</p>@enderror
                                </div>
                            </div>
                        </div>
                        <button type="submit" :disabled="submitting" class="btn-primary w-full mt-6 py-3.5 text-sm font-semibold shadow-card disabled:opacity-60">
                            <span x-show="!submitting">Pay LKR {{ number_format((float) $depositPayment->amount, 2) }}</span>
                            <span x-show="submitting">Processing Demo Payment...</span>
                        </button>
                        <p class="text-[11px] text-center text-coffee-500 mt-3">Card details are validated for this request only and are never saved.</p>
                    </form>
                </div>
            </div>
        @endif
        @if($reservationCreated && !$paymentConfirmed && !$showPaymentForm && !$depositPayment)
            <div x-show="true" class="fixed inset-0 z-40 flex items-center justify-center p-4 bg-coffee-950/60">
                <div class="bg-white rounded-3xl p-8 max-w-md w-full border border-cream-200 shadow-2xl text-center space-y-4">
                    <h2 class="font-serif text-2xl font-bold text-coffee-950">Reservation Request Received</h2>
                    <p class="text-xs text-coffee-600">No deposit is required for this reservation.</p>
                    <a href="{{ route('customer.reservations') }}" class="btn-primary w-full py-2.5 text-xs font-semibold">View My Reservations</a>
                </div>
            </div>
        @endif
        @if($paymentConfirmed && $reservation && $depositPayment)
            <div x-show="confirmed" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-coffee-950/60 backdrop-blur-sm" role="dialog" aria-modal="true" aria-labelledby="payment-confirmed-title">
                <div class="relative bg-white rounded-3xl p-8 max-w-md w-full border border-cream-200 shadow-2xl text-center space-y-4">
                    <button type="button" @click="confirmed = false" aria-label="Close payment confirmation"
                            class="z-10 w-9 h-9 rounded-full border border-cream-200 bg-white text-coffee-600 hover:bg-cream-50 font-semibold flex items-center justify-center"
                            style="position:absolute;top:1rem;right:1rem;">&#x2715;</button>
                    <div class="w-16 h-16 rounded-full bg-sage-100 text-sage-700 mx-auto flex items-center justify-center mt-6" aria-hidden="true">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <h2 id="payment-confirmed-title" class="font-serif text-2xl font-bold text-coffee-950">Payment Confirmed</h2>
                    <h3 class="font-serif text-xl font-bold text-coffee-900">Reservation Confirmed</h3>
                    <p class="text-xs text-coffee-600 leading-relaxed">
                        Your reservation at <strong>{{ $reservation->cafe->name }}</strong> is confirmed for {{ $date }} at {{ $time }}.
                    </p>
                    <p class="text-[11px] text-coffee-500">Demo payment processed securely within CafeFlow; no external payment gateway was used.</p>
                    <div class="p-3 bg-cream-50 rounded-xl text-xs text-coffee-700 border border-cream-200 text-left space-y-1">
                        <div>Cafe: <strong>{{ $reservation->cafe->name }}</strong></div>
                        <div>Table: <strong>{{ $reservationTableName }}</strong></div>
                        <div>Reservation: <strong class="font-mono">{{ $reservationReference }}</strong></div>
                        <div>Payment Status: <strong>Paid</strong></div>
                        <div>Amount Paid: <strong>LKR {{ number_format((float) $depositPayment->amount, 2) }}</strong></div>
                    </div>
                    <a href="{{ route('customer.reservations') }}" class="btn-primary w-full py-2.5 text-xs font-semibold">View My Reservations</a>
                </div>
            </div>
        @endif
    </div>
</x-dashboard-layout>
