{{-- Cafe Details Page matching CafeFlow vintage design reference --}}
<x-dashboard-layout dashboard-role="customer">
    <x-slot name="title">{{ $cafe['name'] }}</x-slot>

    <div class="space-y-8" x-data="{
        activeTab: 'tables', // Default to tables as shown in reference panel 3
        selectedDate: '2026-10-04',
        displayDate: 'Fri, Oct 4, 2026',
        selectedTime: '10:30 AM',
        selectedGuests: '2 Guests',
        selectedTableFilter: 'all',
        selectedTable: {{ json_encode($tables[1] ?? $tables[0]) }},
        isFavourite: {{ $cafe['is_favourite'] ? 'true' : 'false' }},

        // Food ordering state matching reference panel 5
        selectedMenuCategory: 'all',
        orderItems: [
            { id: 101, name: 'Cappuccino', price: 750, qty: 1, image: 'https://images.unsplash.com/photo-1534778101976-62847782c213?auto=format&fit=crop&w=160&q=80' },
            { id: 103, name: 'Avocado Toast', price: 1250, qty: 1, image: 'https://images.unsplash.com/photo-1525351484163-7529414344d8?auto=format&fit=crop&w=160&q=80' },
            { id: 106, name: 'Chocolate Cake', price: 950, qty: 1, image: 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?auto=format&fit=crop&w=160&q=80' }
        ],
        orderFulfillment: 'at_cafe',
        orderDrawerOpen: false,

        addToOrder(item) {
            const existing = this.orderItems.find(i => i.id === item.id);
            if (existing) {
                existing.qty++;
            } else {
                this.orderItems.push({
                    id: item.id,
                    name: item.name,
                    price: item.price,
                    qty: 1,
                    image: item.image
                });
            }
        },
        incrementItem(index) {
            this.orderItems[index].qty++;
        },
        decrementItem(index) {
            if (this.orderItems[index].qty > 1) {
                this.orderItems[index].qty--;
            } else {
                this.orderItems.splice(index, 1);
            }
        },
        removeItem(index) {
            this.orderItems.splice(index, 1);
        },
        get orderSubtotal() {
            return this.orderItems.reduce((sum, item) => sum + (item.price * item.qty), 0);
        }
    }">

        {{-- Top Navigation: Back to Explore --}}
        <div class="flex items-center justify-between">
            <a href="{{ route('customer.explore') }}"
               class="inline-flex items-center gap-2 text-xs font-semibold text-coffee-600 hover:text-accent-600 transition-colors">
                <div class="w-8 h-8 rounded-xl bg-white border border-cream-200 flex items-center justify-center shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </div>
                <span>Back to Cafes</span>
            </a>

            <div class="flex items-center gap-2">
                <button @click="isFavourite = !isFavourite"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white border border-cream-200 text-xs font-medium text-coffee-700 hover:bg-cream-50 transition-all shadow-xs">
                    <svg class="w-4 h-4 transition-colors duration-200"
                         :class="isFavourite ? 'fill-rose-500 text-rose-500' : 'text-coffee-500 fill-none'"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                    <span x-text="isFavourite ? 'Saved in Favourites' : 'Save Cafe'"></span>
                </button>
            </div>
        </div>

        {{-- Hero Cover Section (Panel 3 in reference) --}}
        <div class="relative rounded-3xl overflow-hidden shadow-card border border-cream-200/90 aspect-16/9 sm:aspect-21/9 bg-coffee-950">
            <img src="{{ $cafe['image'] }}" alt="{{ $cafe['name'] }}" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-coffee-950/85 via-coffee-950/40 to-black/30"></div>

            {{-- Cafe Info Overlay --}}
            <div class="absolute bottom-6 inset-x-6 sm:inset-x-8 text-white z-10">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2.5 mb-2">
                            <h1 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight text-white">
                                {{ $cafe['name'] }}
                            </h1>
                            @if($cafe['verified'] ?? true)
                                <span class="w-6 h-6 rounded-full bg-accent-500 text-white flex items-center justify-center shadow-xs" title="Verified Artisanal Cafe">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </span>
                            @endif
                        </div>

                        <div class="flex flex-wrap items-center gap-3 text-xs sm:text-sm text-cream-100">
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-accent-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span>{{ $cafe['location'] }}</span>
                            </span>
                            <span>•</span>
                            <span class="flex items-center gap-1 font-semibold text-brass-300">
                                <svg class="w-4 h-4 text-brass-400 fill-brass-400" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <span>{{ $cafe['rating'] }}</span>
                                <span class="text-cream-200/80 font-normal">({{ $cafe['reviews_count'] }} reviews)</span>
                            </span>
                        </div>

                        {{-- Tag Pills --}}
                        <div class="flex flex-wrap gap-2 mt-3.5">
                            @foreach($cafe['tags'] as $tag)
                                <span class="px-3 py-1 rounded-full text-xs backdrop-blur-md bg-white/20 text-white border border-white/20 font-medium">
                                    {{ $tag }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 5 Navigation Tabs (Overview, Tables, Menu, Reviews, Location) --}}
        <div class="border-b border-cream-200">
            <nav class="flex items-center gap-6 sm:gap-8 overflow-x-auto scrollbar-none text-sm font-medium">
                <button @click="activeTab = 'overview'"
                        class="pb-3 border-b-2 transition-all shrink-0 font-medium"
                        :class="activeTab === 'overview' ? 'border-accent-500 text-coffee-950 font-bold' : 'border-transparent text-coffee-500 hover:text-coffee-800'">
                    Overview
                </button>
                <button @click="activeTab = 'tables'"
                        class="pb-3 border-b-2 transition-all shrink-0 font-medium"
                        :class="activeTab === 'tables' ? 'border-accent-500 text-coffee-950 font-bold' : 'border-transparent text-coffee-500 hover:text-coffee-800'">
                    Tables & Availability
                </button>
                <button @click="activeTab = 'menu'"
                        class="pb-3 border-b-2 transition-all shrink-0 font-medium"
                        :class="activeTab === 'menu' ? 'border-accent-500 text-coffee-950 font-bold' : 'border-transparent text-coffee-500 hover:text-coffee-800'">
                    Menu & Ordering
                </button>
                <button @click="activeTab = 'reviews'"
                        class="pb-3 border-b-2 transition-all shrink-0 font-medium"
                        :class="activeTab === 'reviews' ? 'border-accent-500 text-coffee-950 font-bold' : 'border-transparent text-coffee-500 hover:text-coffee-800'">
                    Reviews ({{ $cafe['reviews_count'] }})
                </button>
                <button @click="activeTab = 'location'"
                        class="pb-3 border-b-2 transition-all shrink-0 font-medium"
                        :class="activeTab === 'location' ? 'border-accent-500 text-coffee-950 font-bold' : 'border-transparent text-coffee-500 hover:text-coffee-800'">
                    Location & Map
                </button>
            </nav>
        </div>

        {{-- TAB 1: OVERVIEW --}}
        <div x-show="activeTab === 'overview'" x-cloak class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                {{-- About the cafe --}}
                <div class="dashboard-card">
                    <h2 class="font-serif text-xl font-bold text-coffee-900 mb-3">About the Cafe</h2>
                    <p class="text-sm text-coffee-600 leading-relaxed font-sans">
                        {{ $cafe['about'] }}
                    </p>
                </div>

                {{-- Features & Amenities --}}
                <div class="dashboard-card">
                    <h2 class="font-serif text-xl font-bold text-coffee-900 mb-4">Cafe Features & Amenities</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($cafe['amenities'] as $amenity)
                            <div class="flex items-center gap-2.5 text-xs text-coffee-700 bg-cream-50 rounded-xl p-2.5 border border-cream-200/70">
                                <div class="w-5 h-5 rounded-full bg-sage-100 text-sage-700 flex items-center justify-center shrink-0">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="font-medium">{{ $amenity }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Photo Gallery --}}
                <div class="dashboard-card">
                    <h2 class="font-serif text-xl font-bold text-coffee-900 mb-4">Space Gallery</h2>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach($cafe['gallery'] as $photo)
                            <div class="aspect-4/3 rounded-xl overflow-hidden bg-cream-100">
                                <img src="{{ $photo }}" alt="{{ $cafe['name'] }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Sidebar details --}}
            <div class="space-y-6">
                {{-- Opening Hours --}}
                <div class="dashboard-card">
                    <h3 class="font-serif text-lg font-bold text-coffee-900 mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-accent-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Opening Hours</span>
                    </h3>
                    <div class="space-y-2 text-xs">
                        @foreach($cafe['opening_hours'] as $days => $hours)
                            <div class="flex items-center justify-between py-1.5 border-b border-cream-100">
                                <span class="text-coffee-600 font-medium">{{ $days }}</span>
                                <span class="text-coffee-900 font-semibold">{{ $hours }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Cafe Info & Pricing --}}
                <div class="dashboard-card space-y-4">
                    <h3 class="font-serif text-lg font-bold text-coffee-900 mb-2">Cafe Details</h3>

                    <div class="text-xs space-y-3">
                        <div>
                            <span class="text-coffee-400 block mb-0.5">Cuisine & Specialties</span>
                            <span class="text-coffee-800 font-medium">{{ $cafe['cuisine'] }}</span>
                        </div>

                        <div>
                            <span class="text-coffee-400 block mb-0.5">Average Spend</span>
                            <span class="text-coffee-800 font-semibold">{{ $cafe['average_price'] }} per person</span>
                        </div>

                        <div>
                            <span class="text-coffee-400 block mb-0.5">Reservation Fee</span>
                            <span class="text-accent-600 font-bold">LKR {{ number_format($cafe['reservation_fee']) }}</span>
                            <p class="text-[11px] text-coffee-500 mt-0.5">Credited in full toward your final bill at the cafe.</p>
                        </div>
                    </div>

                    <button @click="activeTab = 'tables'" class="btn-primary w-full text-xs font-semibold py-2.5">
                        Book a Table
                    </button>
                </div>
            </div>
        </div>

        {{-- TAB 2: TABLES & TABLE AVAILABILITY (Panel 3 in reference) --}}
        <div x-show="activeTab === 'tables'" x-cloak class="space-y-8">
            <div class="dashboard-card bg-[#FAF7F2] border-cream-200">
                <div class="max-w-2xl mb-6">
                    <h2 class="font-serif text-2xl font-bold text-coffee-950 tracking-tight">Table Availability</h2>
                    <p class="text-xs sm:text-sm text-coffee-600 mt-1">Select your preferred date, time and table to proceed.</p>
                </div>

                {{-- Interactive Pickers for Date, Time, Guests --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                    {{-- Date Picker --}}
                    <div>
                        <label class="block text-xs font-semibold text-coffee-700 mb-1.5">Date</label>
                        <div class="relative">
                            <input type="text"
                                   x-model="displayDate"
                                   class="w-full bg-white text-xs sm:text-sm text-coffee-800 font-medium rounded-xl border border-cream-200/90 py-2.5 px-3 focus:ring-2 focus:ring-accent-400/20">
                        </div>
                    </div>

                    {{-- Time Picker --}}
                    <div>
                        <label class="block text-xs font-semibold text-coffee-700 mb-1.5">Time</label>
                        <select x-model="selectedTime" class="w-full bg-white text-xs sm:text-sm text-coffee-800 font-medium rounded-xl border border-cream-200/90 py-2.5 px-3 focus:ring-2 focus:ring-accent-400/20">
                            <option>09:30 AM</option>
                            <option>10:00 AM</option>
                            <option>10:30 AM</option>
                            <option>11:00 AM</option>
                            <option>02:00 PM</option>
                            <option>04:30 PM</option>
                            <option>06:00 PM</option>
                            <option>07:30 PM</option>
                        </select>
                    </div>

                    {{-- Guests Picker --}}
                    <div>
                        <label class="block text-xs font-semibold text-coffee-700 mb-1.5">Number of Guests</label>
                        <select x-model="selectedGuests" class="w-full bg-white text-xs sm:text-sm text-coffee-800 font-medium rounded-xl border border-cream-200/90 py-2.5 px-3 focus:ring-2 focus:ring-accent-400/20">
                            <option>1 Guest</option>
                            <option>2 Guests</option>
                            <option>3 Guests</option>
                            <option>4 Guests</option>
                            <option>6 Guests</option>
                        </select>
                    </div>
                </div>

                {{-- Table Category Filter Pills --}}
                <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none text-xs border-t border-cream-200/60 pt-4">
                    <button @click="selectedTableFilter = 'all'"
                            class="px-3.5 py-1.5 rounded-full font-medium transition-all"
                            :class="selectedTableFilter === 'all' ? 'bg-coffee-900 text-cream-50 font-semibold' : 'bg-white text-coffee-700 border border-cream-200'">
                        All Tables
                    </button>
                    <button @click="selectedTableFilter = 'indoor'"
                            class="px-3.5 py-1.5 rounded-full font-medium transition-all"
                            :class="selectedTableFilter === 'indoor' ? 'bg-coffee-900 text-cream-50 font-semibold' : 'bg-white text-coffee-700 border border-cream-200'">
                        Indoor
                    </button>
                    <button @click="selectedTableFilter = 'outdoor'"
                            class="px-3.5 py-1.5 rounded-full font-medium transition-all"
                            :class="selectedTableFilter === 'outdoor' ? 'bg-coffee-900 text-cream-50 font-semibold' : 'bg-white text-coffee-700 border border-cream-200'">
                        Outdoor
                    </button>
                    <button @click="selectedTableFilter = 'rooftop'"
                            class="px-3.5 py-1.5 rounded-full font-medium transition-all"
                            :class="selectedTableFilter === 'rooftop' ? 'bg-coffee-900 text-cream-50 font-semibold' : 'bg-white text-coffee-700 border border-cream-200'">
                        Rooftop
                    </button>
                    <button @click="selectedTableFilter = 'window'"
                            class="px-3.5 py-1.5 rounded-full font-medium transition-all"
                            :class="selectedTableFilter === 'window' ? 'bg-coffee-900 text-cream-50 font-semibold' : 'bg-white text-coffee-700 border border-cream-200'">
                        Window Seat
                    </button>
                </div>
            </div>

            {{-- Table Cards Grid (Matches Reference Panel 3) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @foreach($tables as $table)
                    <div x-show="selectedTableFilter === 'all' || selectedTableFilter === '{{ $table['category'] }}'"
                         @click="selectedTable = {{ json_encode($table) }}"
                         class="cursor-pointer rounded-2xl bg-white border overflow-hidden shadow-subtle transition-all duration-200 flex flex-col justify-between"
                         :class="selectedTable && selectedTable.id === {{ $table['id'] }} ? 'border-accent-500 ring-2 ring-accent-400/30 shadow-card-hover -translate-y-1' : 'border-cream-200 hover:border-cream-300 hover:shadow-card'">

                        <div>
                            {{-- Table Photo --}}
                            <div class="relative aspect-4/3 overflow-hidden bg-cream-100">
                                <img src="{{ $table['image'] }}" alt="{{ $table['name'] }}" class="w-full h-full object-cover">

                                {{-- Selected Indicator Overlay Checkmark --}}
                                <div x-show="selectedTable && selectedTable.id === {{ $table['id'] }}"
                                     class="absolute top-2 right-2 w-7 h-7 rounded-full bg-accent-500 text-white flex items-center justify-center shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                </div>
                            </div>

                            <div class="p-4">
                                <div class="flex items-center justify-between mb-1">
                                    <h3 class="font-serif text-base font-bold text-coffee-900">{{ $table['name'] }}</h3>
                                    <span class="text-[10px] font-semibold text-coffee-600 bg-cream-100 px-2 py-0.5 rounded-md border border-cream-200">
                                        {{ $table['type'] }}
                                    </span>
                                </div>

                                <p class="text-xs text-coffee-500 flex items-center gap-1.5 mb-2">
                                    <svg class="w-3.5 h-3.5 text-coffee-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    <span>{{ $table['capacity'] }}</span>
                                    <span>•</span>
                                    <span>{{ $table['location'] }}</span>
                                </p>
                            </div>
                        </div>

                        {{-- Table Footer with Availability Badge --}}
                        <div class="px-4 pb-4 pt-1 flex items-center justify-between border-t border-cream-50">
                            @if($table['status'] === 'available')
                                <span class="badge-sage text-[11px]">
                                    <span class="w-1.5 h-1.5 rounded-full bg-sage-500 animate-pulse"></span>
                                    <span>Available</span>
                                </span>
                            @elseif($table['status'] === 'almost_full')
                                <span class="badge bg-amber-50 text-amber-700 border border-amber-200/60 text-[11px]">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    <span>Almost full</span>
                                </span>
                            @else
                                <span class="badge bg-rose-50 text-rose-700 border border-rose-200/60 text-[11px]">
                                    <span>Unavailable</span>
                                </span>
                            @endif

                            <span class="text-xs font-semibold"
                                  :class="selectedTable && selectedTable.id === {{ $table['id'] }} ? 'text-accent-600 font-bold' : 'text-coffee-400'">
                                <span x-text="selectedTable && selectedTable.id === {{ $table['id'] }} ? 'Selected' : 'Select'"></span>
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Floating / Sticky Proceed to Reservation Checkout Bar --}}
            <div x-show="selectedTable"
                 x-transition
                 class="glass-card-warm rounded-3xl p-5 sm:p-6 border border-cream-300 shadow-card flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-accent-50 border border-accent-200 flex items-center justify-center text-accent-600 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h4 class="font-serif text-base font-bold text-coffee-950">
                                <span x-text="selectedTable ? selectedTable.name : ''"></span> Selected
                            </h4>
                            <span class="badge-sage text-[10px]" x-text="selectedGuests"></span>
                        </div>
                        <p class="text-xs text-coffee-600 mt-0.5">
                            Reservation Fee: <strong class="text-coffee-900">LKR {{ number_format($cafe['reservation_fee']) }}</strong>
                            <span class="text-coffee-400 text-[11px]">(Credited toward your final bill)</span>
                        </p>
                    </div>
                </div>

                <a :href="'{{ route('customer.reservation.checkout') }}?cafe={{ $cafe['slug'] }}&table=' + (selectedTable ? selectedTable.id : 2) + '&date=' + encodeURIComponent(displayDate) + '&time=' + encodeURIComponent(selectedTime) + '&guests=' + encodeURIComponent(selectedGuests)"
                   class="btn-primary py-3 px-8 text-sm font-semibold tracking-tight shadow-sm shrink-0 w-full sm:w-auto text-center">
                    <span>Proceed to Reservation</span>
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>

        {{-- TAB 3: MENU & FOOD ORDERING (Matches Reference Panel 5) --}}
        <div x-show="activeTab === 'menu'" x-cloak class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            {{-- Menu Items List (8 cols) --}}
            <div class="lg:col-span-8 space-y-6">
                {{-- Category Filter Pills --}}
                <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none text-xs">
                    <button @click="selectedMenuCategory = 'all'"
                            class="px-4 py-2 rounded-full font-medium transition-all"
                            :class="selectedMenuCategory === 'all' ? 'bg-coffee-900 text-cream-50 font-semibold' : 'bg-white text-coffee-700 border border-cream-200'">
                        All
                    </button>
                    <button @click="selectedMenuCategory = 'Coffee'"
                            class="px-4 py-2 rounded-full font-medium transition-all"
                            :class="selectedMenuCategory === 'Coffee' ? 'bg-coffee-900 text-cream-50 font-semibold' : 'bg-white text-coffee-700 border border-cream-200'">
                        Coffee
                    </button>
                    <button @click="selectedMenuCategory = 'Breakfast'"
                            class="px-4 py-2 rounded-full font-medium transition-all"
                            :class="selectedMenuCategory === 'Breakfast' ? 'bg-coffee-900 text-cream-50 font-semibold' : 'bg-white text-coffee-700 border border-cream-200'">
                        Breakfast
                    </button>
                    <button @click="selectedMenuCategory = 'Main Course'"
                            class="px-4 py-2 rounded-full font-medium transition-all"
                            :class="selectedMenuCategory === 'Main Course' ? 'bg-coffee-900 text-cream-50 font-semibold' : 'bg-white text-coffee-700 border border-cream-200'">
                        Main Course
                    </button>
                    <button @click="selectedMenuCategory = 'Desserts'"
                            class="px-4 py-2 rounded-full font-medium transition-all"
                            :class="selectedMenuCategory === 'Desserts' ? 'bg-coffee-900 text-cream-50 font-semibold' : 'bg-white text-coffee-700 border border-cream-200'">
                        Desserts
                    </button>
                    <button @click="selectedMenuCategory = 'Drinks'"
                            class="px-4 py-2 rounded-full font-medium transition-all"
                            :class="selectedMenuCategory === 'Drinks' ? 'bg-coffee-900 text-cream-50 font-semibold' : 'bg-white text-coffee-700 border border-cream-200'">
                        Drinks
                    </button>
                </div>

                {{-- Food Cards 2 or 3 columns --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5">
                    @foreach($menu as $item)
                        <div x-show="selectedMenuCategory === 'all' || selectedMenuCategory === '{{ $item['category'] }}'"
                             class="bg-white rounded-2xl border border-cream-200 overflow-hidden shadow-subtle hover:shadow-card transition-all flex flex-col justify-between">
                            <div>
                                <div class="aspect-4/3 overflow-hidden bg-cream-100">
                                    <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                                </div>
                                <div class="p-3.5">
                                    <h4 class="font-serif text-sm font-bold text-coffee-900 leading-snug">{{ $item['name'] }}</h4>
                                    <p class="text-[11px] text-coffee-500 mt-1 line-clamp-2 leading-relaxed">{{ $item['description'] }}</p>
                                </div>
                            </div>

                            <div class="px-3.5 pb-3.5 pt-2 flex items-center justify-between border-t border-cream-50">
                                <span class="font-semibold text-xs text-coffee-900">{{ $item['formatted_price'] }}</span>
                                <button @click="addToOrder({{ json_encode($item) }})"
                                        class="w-7 h-7 rounded-lg bg-accent-500 hover:bg-accent-600 text-white flex items-center justify-center shadow-xs transition-transform active:scale-90">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- "Your Order" Drawer / Card (Panel 5 in reference) --}}
            <div class="lg:col-span-4">
                <div class="dashboard-card bg-[#FAF7F2] border-cream-300 sticky top-28 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-cream-200">
                        <div class="flex items-center gap-2">
                            <h3 class="font-serif text-base font-bold text-coffee-900">Your Order</h3>
                            <span class="badge-coffee text-[10px]" x-text="orderItems.length + ' items'"></span>
                        </div>
                        <button x-show="orderItems.length > 0" @click="orderItems = []" class="text-[11px] text-rose-500 hover:underline">Clear</button>
                    </div>

                    {{-- Items list --}}
                    <div class="space-y-3 max-h-72 overflow-y-auto pr-1">
                        <template x-for="(item, index) in orderItems" :key="item.id">
                            <div class="flex items-center justify-between gap-2.5 pb-2.5 border-b border-cream-100">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <img :src="item.image" :alt="item.name" class="w-10 h-10 rounded-lg object-cover shrink-0">
                                    <div class="min-w-0">
                                        <p class="text-xs font-semibold text-coffee-800 truncate" x-text="item.name"></p>
                                        <p class="text-[11px] text-coffee-500" x-text="'LKR ' + item.price.toLocaleString()"></p>
                                    </div>
                                </div>

                                {{-- Quantity Controls --}}
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <div class="flex items-center bg-white rounded-lg border border-cream-200 p-0.5">
                                        <button @click="decrementItem(index)" class="w-5 h-5 flex items-center justify-center text-coffee-500 hover:text-coffee-900 text-xs font-bold">-</button>
                                        <span class="px-1.5 text-xs font-medium text-coffee-800" x-text="item.qty"></span>
                                        <button @click="incrementItem(index)" class="w-5 h-5 flex items-center justify-center text-coffee-500 hover:text-coffee-900 text-xs font-bold">+</button>
                                    </div>
                                    <button @click="removeItem(index)" class="text-coffee-400 hover:text-rose-500 p-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>
                        </template>

                        <div x-show="orderItems.length === 0" class="text-center py-6 text-xs text-coffee-400">
                            Your order is empty. Click + on any food item to add.
                        </div>
                    </div>

                    {{-- Subtotal & Pre-order options --}}
                    <div class="pt-2 border-t border-cream-200 space-y-3">
                        <div class="flex items-center justify-between text-xs font-semibold text-coffee-900">
                            <span>Subtotal</span>
                            <span class="text-sm font-bold text-accent-600" x-text="'LKR ' + orderSubtotal.toLocaleString()"></span>
                        </div>

                        {{-- Options requested: "Order at Cafe" or "Pre-order for my reservation" --}}
                        <div class="space-y-2 pt-2 border-t border-cream-100 text-xs">
                            <label class="flex items-center gap-2 text-coffee-700 cursor-pointer">
                                <input type="radio" name="orderFulfillment" value="at_cafe" x-model="orderFulfillment" class="text-accent-500 focus:ring-accent-400">
                                <span>Order at Cafe</span>
                            </label>
                            <label class="flex items-center gap-2 text-coffee-700 cursor-pointer">
                                <input type="radio" name="orderFulfillment" value="preorder" x-model="orderFulfillment" class="text-accent-500 focus:ring-accent-400">
                                <span>Pre-order (Ready on arrival)</span>
                            </label>
                        </div>

                        <button @click="activeTab = 'tables'" class="btn-primary w-full py-2.5 text-xs font-semibold">
                            Add to Reservation
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB 4: REVIEWS --}}
        <div x-show="activeTab === 'reviews'" x-cloak class="dashboard-card space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-cream-200">
                <div>
                    <h2 class="font-serif text-2xl font-bold text-coffee-900">Customer Testimonials</h2>
                    <p class="text-xs text-coffee-500 mt-0.5">Real verified visits and reservation reviews.</p>
                </div>
                <div class="flex items-center gap-3 bg-cream-50 px-4 py-2 rounded-2xl border border-cream-200">
                    <span class="font-serif text-3xl font-bold text-coffee-900">{{ $cafe['rating'] }}</span>
                    <div>
                        <div class="flex text-brass-500">★★★★★</div>
                        <span class="text-[11px] text-coffee-500">{{ $cafe['reviews_count'] }} total ratings</span>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <div class="p-4 rounded-2xl bg-cream-50/70 border border-cream-100 space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full bg-cream-200 font-serif font-bold text-coffee-800 text-xs flex items-center justify-center">M</div>
                            <div>
                                <span class="text-xs font-bold text-coffee-900">Minura Fernando</span>
                                <span class="badge-sage text-[10px] ml-2">Verified Visit</span>
                            </div>
                        </div>
                        <span class="text-[11px] text-coffee-400">2 days ago</span>
                    </div>
                    <p class="text-xs text-coffee-600 leading-relaxed font-sans">
                        "The window booth reservation was ready as soon as we arrived. Impeccable pour-over and the sourdough toast was divine. CafeFlow made booking effortless."
                    </p>
                </div>

                <div class="p-4 rounded-2xl bg-cream-50/70 border border-cream-100 space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full bg-cream-200 font-serif font-bold text-coffee-800 text-xs flex items-center justify-center">S</div>
                            <div>
                                <span class="text-xs font-bold text-coffee-900">Senuri Perera</span>
                                <span class="badge-sage text-[10px] ml-2">Verified Visit</span>
                            </div>
                        </div>
                        <span class="text-[11px] text-coffee-400">1 week ago</span>
                    </div>
                    <p class="text-xs text-coffee-600 leading-relaxed font-sans">
                        "Best remote working sanctuary in Colombo 07. Fast Wi-Fi, quiet corners, and friendly baristas."
                    </p>
                </div>
            </div>
        </div>

        {{-- TAB 5: LOCATION --}}
        <div x-show="activeTab === 'location'" x-cloak class="dashboard-card space-y-6">
            <h2 class="font-serif text-2xl font-bold text-coffee-900">Location & Neighborhood</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                <div class="aspect-16/10 rounded-2xl overflow-hidden bg-cream-100 border border-cream-200 relative">
                    {{-- Realistic map simulation styled with warm cafe tones --}}
                    <div class="w-full h-full bg-[#EFE9E0] flex flex-col items-center justify-center p-6 text-center">
                        <div class="w-12 h-12 rounded-full bg-accent-500 text-white flex items-center justify-center shadow-card mb-2 animate-bounce">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <p class="font-serif font-bold text-coffee-900 text-sm">{{ $cafe['name'] }}</p>
                        <p class="text-xs text-coffee-500 max-w-xs mt-1">{{ $cafe['address'] }}</p>
                    </div>
                </div>

                <div class="space-y-4 text-xs">
                    <div>
                        <h4 class="font-bold text-coffee-800 mb-1">Full Address</h4>
                        <p class="text-coffee-600">{{ $cafe['address'] }}</p>
                    </div>
                    <div>
                        <h4 class="font-bold text-coffee-800 mb-1">Parking Availability</h4>
                        <p class="text-coffee-600">Free dedicated customer parking lot and convenient valet at entrance.</p>
                    </div>
                    <div>
                        <h4 class="font-bold text-coffee-800 mb-1">Directions</h4>
                        <p class="text-coffee-600">Situated in Cinnamon Gardens, adjacent to the public park, easily accessible by taxi or car.</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-dashboard-layout>
