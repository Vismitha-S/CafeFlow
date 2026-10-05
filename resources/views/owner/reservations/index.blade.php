<x-dashboard-layout dashboard-role="owner">
    <x-slot name="title">Reservations - {{ $cafe->name }}</x-slot>
    <x-slot name="header">Reservations</x-slot>

    <div class="space-y-6" x-data="{
        selectedRes: null,
        showDetailModal: false,
        showCancelModal: false,
        cancelActionUrl: '',
        openDetails(res) {
            this.selectedRes = res;
            this.showDetailModal = true;
        },
        openCancel(id) {
            this.cancelActionUrl = '{{ url('/owner/reservations') }}/' + id + '/cancel';
            this.showCancelModal = true;
        }
    }">

        {{-- Top Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-cream-200">
            <div>
                <h1 class="font-serif text-2xl sm:text-3xl font-bold text-coffee-950 tracking-tight">
                    Reservation Bookings
                </h1>
                <p class="text-xs sm:text-sm text-coffee-500 mt-0.5">
                    Live schedule, guest bookings, deposit payments, and seating allocation for <span class="font-semibold text-coffee-800">{{ $cafe->name }}</span>.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <span class="badge-sage text-xs font-semibold px-3 py-1.5 shadow-2xs">
                    <span>{{ $todayCount }} Bookings Today</span>
                </span>
            </div>
        </div>

        {{-- Filters & Search Controls --}}
        <div class="dashboard-card p-4 space-y-4">
            <form method="GET" action="{{ route('owner.reservations.index') }}" class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-3">
                {{-- Time Filter Tabs --}}
                <div class="flex flex-wrap items-center gap-1.5 bg-cream-100/80 p-1 rounded-xl text-xs font-semibold">
                    <a href="{{ route('owner.reservations.index', ['time_filter' => 'all', 'status' => $statusFilter, 'search' => $search]) }}"
                       class="px-3 py-1.5 rounded-lg transition-all {{ $timeFilter === 'all' ? 'bg-white text-coffee-900 shadow-xs' : 'text-coffee-600 hover:text-coffee-900' }}">
                        All ({{ $totalCount }})
                    </a>
                    <a href="{{ route('owner.reservations.index', ['time_filter' => 'today', 'status' => $statusFilter, 'search' => $search]) }}"
                       class="px-3 py-1.5 rounded-lg transition-all {{ $timeFilter === 'today' ? 'bg-white text-coffee-900 shadow-xs' : 'text-coffee-600 hover:text-coffee-900' }}">
                        Today ({{ $todayCount }})
                    </a>
                    <a href="{{ route('owner.reservations.index', ['time_filter' => 'upcoming', 'status' => $statusFilter, 'search' => $search]) }}"
                       class="px-3 py-1.5 rounded-lg transition-all {{ $timeFilter === 'upcoming' ? 'bg-white text-coffee-900 shadow-xs' : 'text-coffee-600 hover:text-coffee-900' }}">
                        Upcoming ({{ $upcomingCount }})
                    </a>
                    <a href="{{ route('owner.reservations.index', ['time_filter' => 'past', 'status' => $statusFilter, 'search' => $search]) }}"
                       class="px-3 py-1.5 rounded-lg transition-all {{ $timeFilter === 'past' ? 'bg-white text-coffee-900 shadow-xs' : 'text-coffee-600 hover:text-coffee-900' }}">
                        Past
                    </a>
                </div>

                {{-- Status & Search Inputs --}}
                <div class="flex flex-col sm:flex-row items-center gap-2.5">
                    <input type="hidden" name="time_filter" value="{{ $timeFilter }}">

                    <select name="status" onchange="this.form.submit()" class="w-full sm:w-auto text-xs font-semibold text-coffee-900 bg-white border-cream-300 rounded-xl px-3 py-2 focus:ring-accent-400">
                        <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>All Statuses</option>
                        <option value="confirmed" {{ $statusFilter === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="completed" {{ $statusFilter === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $statusFilter === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>

                    <div class="relative w-full sm:w-64">
                        <input type="text" name="search" value="{{ $search }}"
                               placeholder="Search customer, table..."
                               class="w-full text-xs rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400 pl-8 bg-white placeholder:text-coffee-400">
                        <svg class="w-4 h-4 text-coffee-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>

                    @if($search || $statusFilter !== 'all' || $timeFilter !== 'all')
                        <a href="{{ route('owner.reservations.index') }}" class="btn-ghost text-xs py-2 px-3">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Reservations List Table --}}
        <div class="dashboard-card p-0 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-cream-100/60 border-b border-cream-200 text-[11px] uppercase tracking-wider text-coffee-600 font-semibold">
                            <th class="py-3.5 px-4 sm:px-6">Booking #</th>
                            <th class="py-3.5 px-4">Guest</th>
                            <th class="py-3.5 px-4">Date & Time</th>
                            <th class="py-3.5 px-4">Table</th>
                            <th class="py-3.5 px-4">Deposit Fee</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4 sm:px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-cream-100 text-xs">
                        @forelse($reservations as $res)
                            <tr class="hover:bg-cream-50/50 transition-colors {{ $highlightId === $res->id ? 'bg-accent-50/60 font-semibold' : '' }}">
                                <td class="py-4 px-4 sm:px-6 font-mono text-coffee-900">
                                    #RES-{{ $res->id }}
                                </td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-full bg-coffee-100 text-coffee-800 font-serif font-bold text-[11px] flex items-center justify-center shrink-0">
                                            {{ strtoupper(substr($res->user?->name ?? 'G', 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-serif font-bold text-coffee-950 truncate">{{ $res->user?->name ?? 'Guest' }}</p>
                                            <p class="text-[11px] text-coffee-500 truncate">{{ $res->user?->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    <p class="font-bold text-coffee-900">{{ \Carbon\Carbon::parse($res->reservation_date)->format('M j, Y') }}</p>
                                    <p class="text-[11px] text-coffee-500 font-mono">
                                        {{ \Carbon\Carbon::parse($res->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($res->end_time)->format('g:i A') }}
                                    </p>
                                </td>
                                <td class="py-4 px-4">
                                    <p class="font-semibold text-coffee-900">{{ $res->cafeTable?->name ?: ('Table '.$res->cafeTable?->table_number) }}</p>
                                    <p class="text-[11px] text-coffee-500 capitalize">{{ $res->cafeTable?->location ?? 'indoor' }} • 👥 {{ $res->guest_count }} Guests</p>
                                </td>
                                <td class="py-4 px-4">
                                    <p class="font-bold text-coffee-900">LKR {{ number_format($res->reservation_fee, 2) }}</p>
                                    @php
                                        $paidPayment = $res->payments->firstWhere('status', 'paid');
                                    @endphp
                                    @if($paidPayment)
                                        <span class="text-[10px] text-sage-700 font-medium">✓ Paid</span>
                                    @else
                                        <span class="text-[10px] text-amber-700 font-medium">Pending Deposit</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4">
                                    @if($res->status === 'confirmed')
                                        <span class="badge-sage text-[10px] font-semibold">Confirmed</span>
                                    @elseif($res->status === 'pending')
                                        <span class="badge bg-amber-50 text-amber-700 border border-amber-200/80 text-[10px] font-semibold">Pending</span>
                                    @elseif($res->status === 'completed')
                                        <span class="badge bg-cream-100 text-coffee-700 border border-cream-200 text-[10px] font-semibold">Completed</span>
                                    @else
                                        <span class="badge bg-rose-50 text-rose-700 border border-rose-200/80 text-[10px] font-semibold">Cancelled</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 sm:px-6 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button type="button" @click="openDetails({{ json_encode($res) }})"
                                                class="px-2.5 py-1 rounded-lg bg-cream-100 hover:bg-cream-200 text-coffee-800 font-semibold text-[11px] transition-colors" title="View details">
                                            Details
                                        </button>

                                        @if(in_array($res->status, ['pending', 'confirmed']))
                                            <form method="POST" action="{{ route('owner.reservations.complete', $res->id) }}">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1 rounded-lg bg-sage-50 hover:bg-sage-100 text-sage-800 font-semibold text-[11px] transition-colors" title="Mark as Completed">
                                                    Complete
                                                </button>
                                            </form>

                                            <button type="button" @click="openCancel({{ $res->id }})"
                                                    class="p-1 rounded-lg text-rose-500 hover:bg-rose-50 hover:text-rose-700" title="Cancel Booking">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-coffee-400">
                                    <p class="font-serif text-base font-bold text-coffee-800">No reservations match the selected criteria</p>
                                    <p class="text-xs text-coffee-500 mt-1">Try adjusting the filter tabs or search parameters.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($reservations->hasPages())
                <div class="p-4 border-t border-cream-200">
                    {{ $reservations->links() }}
                </div>
            @endif
        </div>

        {{-- Reservation Details Modal --}}
        <div x-show="showDetailModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 text-center sm:block sm:p-0">
                <div @click="showDetailModal = false" class="fixed inset-0 bg-coffee-950/60 backdrop-blur-xs"></div>

                <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-card transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-cream-200">
                    <div class="p-6 space-y-5" x-show="selectedRes">
                        <div class="flex items-center justify-between pb-3 border-b border-cream-100">
                            <div>
                                <span class="text-[11px] font-mono text-coffee-400">#RES-<span x-text="selectedRes?.id"></span></span>
                                <h3 class="font-serif text-lg font-bold text-coffee-950">Reservation Breakdown</h3>
                            </div>
                            <button type="button" @click="showDetailModal = false" class="text-coffee-400 hover:text-coffee-700">✕</button>
                        </div>

                        <div class="space-y-3 text-xs">
                            <div class="p-3.5 bg-cream-50 rounded-2xl border border-cream-200 space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-coffee-500">Customer:</span>
                                    <span class="font-bold text-coffee-950" x-text="selectedRes?.user?.name || 'Guest'"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-coffee-500">Email:</span>
                                    <span class="font-medium text-coffee-800" x-text="selectedRes?.user?.email || 'N/A'"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-coffee-500">Date:</span>
                                    <span class="font-bold text-coffee-900" x-text="selectedRes?.reservation_date"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-coffee-500">Schedule:</span>
                                    <span class="font-mono text-coffee-900"><span x-text="selectedRes?.start_time"></span> - <span x-text="selectedRes?.end_time"></span></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-coffee-500">Table:</span>
                                    <span class="font-semibold text-coffee-900" x-text="selectedRes?.cafe_table?.name || ('Table ' + selectedRes?.cafe_table?.table_number)"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-coffee-500">Party Size:</span>
                                    <span class="font-bold text-coffee-900"><span x-text="selectedRes?.guest_count"></span> Guests</span>
                                </div>
                            </div>

                            <div class="p-3.5 bg-cream-50 rounded-2xl border border-cream-200 space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-coffee-500">Reservation Deposit:</span>
                                    <span class="font-bold text-coffee-900">LKR <span x-text="Number(selectedRes?.reservation_fee || 0).toFixed(2)"></span></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-coffee-500">Booking Status:</span>
                                    <span class="font-bold uppercase text-coffee-900" x-text="selectedRes?.status"></span>
                                </div>

                                <template x-if="selectedRes?.status === 'cancelled'">
                                    <div class="pt-2 border-t border-cream-200 text-rose-800 space-y-1">
                                        <div class="flex justify-between font-semibold">
                                            <span>Cancellation Deduction Retained (<span x-text="Number(selectedRes?.cancellation_penalty_percentage || 50).toFixed(0)"></span>%):</span>
                                            <span>LKR <span x-text="Number(selectedRes?.cancellation_penalty_amount != null ? selectedRes.cancellation_penalty_amount : ((Number(selectedRes?.reservation_fee || 0) * Number(selectedRes?.cancellation_penalty_percentage || 50)) / 100)).toFixed(2)"></span></span>
                                        </div>
                                        <div class="flex justify-between text-sage-800 font-semibold">
                                            <span>Refund to Customer:</span>
                                            <span>LKR <span x-text="Math.max(0, Number(selectedRes?.reservation_fee || 0) - Number(selectedRes?.cancellation_penalty_amount != null ? selectedRes.cancellation_penalty_amount : ((Number(selectedRes?.reservation_fee || 0) * Number(selectedRes?.cancellation_penalty_percentage || 50)) / 100))).toFixed(2)"></span></span>
                                        </div>
                                        <template x-if="selectedRes?.cancellation_reason">
                                            <p class="text-[11px] text-coffee-600 pt-0.5">Reason: <span class="italic text-coffee-800" x-text="selectedRes.cancellation_reason"></span></p>
                                        </template>
                                        <p class="text-[10px] text-coffee-500 pt-1 italic">The reserved table slot was immediately made available back for new bookings upon cancellation.</p>
                                    </div>
                                </template>
                            </div>

                            <template x-if="selectedRes?.notes">
                                <div class="p-3 bg-cream-50/60 rounded-xl border border-cream-200">
                                    <span class="text-[11px] text-coffee-400 block">Guest Notes:</span>
                                    <p class="text-xs text-coffee-800 mt-0.5" x-text="selectedRes?.notes"></p>
                                </div>
                            </template>
                        </div>

                        <div class="flex items-center justify-end pt-3 border-t border-cream-100">
                            <button type="button" @click="showDetailModal = false" class="btn-primary text-xs">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Cancel Confirmation Modal --}}
        <div x-show="showCancelModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 text-center sm:block sm:p-0">
                <div @click="showCancelModal = false" class="fixed inset-0 bg-coffee-950/60 backdrop-blur-xs"></div>

                <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-card transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-cream-200">
                    <form :action="cancelActionUrl" method="POST" class="p-6 space-y-4">
                        @csrf
                        <div class="flex items-center justify-between pb-3 border-b border-cream-100">
                            <h3 class="font-serif text-lg font-bold text-rose-900">Cancel Reservation</h3>
                            <button type="button" @click="showCancelModal = false" class="text-coffee-400 hover:text-coffee-700">✕</button>
                        </div>

                        <p class="text-xs text-coffee-600">
                            Are you sure you want to cancel this reservation? The standard cancellation fee deduction will be applied and the slot will be released back for bookings.
                        </p>

                        <div class="space-y-1">
                            <label class="block text-xs font-semibold text-coffee-800">Cancellation Reason</label>
                            <textarea name="cancellation_reason" rows="2" placeholder="e.g. Cafe maintenance, emergency closure..."
                                      class="w-full text-sm rounded-xl border-cream-300 focus:border-rose-500 focus:ring-rose-400"></textarea>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-3 border-t border-cream-100">
                            <button type="button" @click="showCancelModal = false" class="btn-ghost text-xs">Back</button>
                            <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-semibold text-xs transition-colors">
                                Confirm Cancellation
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-dashboard-layout>
