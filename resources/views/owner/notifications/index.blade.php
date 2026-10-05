<x-dashboard-layout dashboard-role="owner">
    <x-slot name="title">Notifications - CafeFlow Owner</x-slot>
    <x-slot name="header">Notifications</x-slot>

    <div class="max-w-4xl mx-auto space-y-6">

        {{-- Top Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-cream-200">
            <div>
                <h1 class="font-serif text-2xl sm:text-3xl font-bold text-coffee-950 tracking-tight">
                    Notifications
                </h1>
                <p class="text-xs sm:text-sm text-coffee-500 mt-0.5">
                    Real-time alerts for customer reservations, booking updates, and table activities.
                </p>
            </div>

            @if($unreadCount > 0)
                <form method="POST" action="{{ route('owner.notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="btn-secondary text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Mark All as Read</span>
                    </button>
                </form>
            @endif
        </div>

        {{-- Notification Cards List --}}
        <div class="space-y-3">
            @forelse($notifications as $notif)
                @php
                    $data = $notif->data;
                    $isRead = $notif->read();
                    $resId = $data['reservation_id'] ?? null;
                @endphp
                <div class="dashboard-card p-4 transition-all {{ $isRead ? 'bg-white' : 'bg-accent-50/30 border-accent-200 shadow-xs' }}">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-start gap-3.5 min-w-0">
                            <div class="w-10 h-10 rounded-2xl flex items-center justify-center shrink-0 {{ $isRead ? 'bg-cream-100 text-coffee-600' : 'bg-accent-100 text-accent-700' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>

                            <div class="min-w-0 space-y-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h4 class="font-serif text-sm font-bold text-coffee-950">{{ $data['title'] ?? 'Notification' }}</h4>
                                    @if(!$isRead)
                                        <span class="badge-accent text-[10px] font-semibold">New</span>
                                    @endif
                                    <span class="text-[10px] text-coffee-400 font-sans">• {{ $notif->created_at->diffForHumans() }}</span>
                                </div>

                                <p class="text-xs text-coffee-700 leading-relaxed">{{ $data['message'] ?? '' }}</p>

                                @if($resId)
                                    <div class="pt-1 flex flex-wrap items-center gap-3 text-[11px] text-coffee-600 font-medium">
                                        <span>👤 Guest: <strong class="text-coffee-900">{{ $data['customer_name'] ?? 'Guest' }}</strong></span>
                                        <span>•</span>
                                        <span>📅 {{ $data['reservation_date'] ?? '' }}</span>
                                        <span>•</span>
                                        <span>⏰ {{ $data['start_time'] ?? '' }} - {{ $data['end_time'] ?? '' }}</span>
                                        <span>•</span>
                                        <span>🪑 {{ $data['table_name'] ?? 'Table' }}</span>
                                        <span>•</span>
                                        <span>👥 {{ $data['guest_count'] ?? 1 }} Guests</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            @if($resId)
                                <form method="POST" action="{{ route('owner.notifications.read', $notif->id) }}">
                                    @csrf
                                    <button type="submit" class="btn-primary text-xs py-1.5 px-3">
                                        View Booking &rarr;
                                    </button>
                                </form>
                            @elseif(!$isRead)
                                <form method="POST" action="{{ route('owner.notifications.read', $notif->id) }}">
                                    @csrf
                                    <button type="submit" class="btn-secondary text-xs py-1.5 px-3">
                                        Mark Read
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="dashboard-card text-center py-16 px-4 space-y-3">
                    <div class="w-12 h-12 rounded-2xl bg-cream-100 text-coffee-400 mx-auto flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    </div>
                    <p class="font-serif text-lg font-bold text-coffee-900">No notifications yet</p>
                    <p class="text-xs text-coffee-500 max-w-sm mx-auto">When customers book tables at your cafe, you will receive real-time notifications here.</p>
                </div>
            @endforelse
        </div>

        @if($notifications->hasPages())
            <div class="p-4 border-t border-cream-200">
                {{ $notifications->links() }}
            </div>
        @endif

    </div>
</x-dashboard-layout>
