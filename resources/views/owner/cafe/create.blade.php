<x-dashboard-layout dashboard-role="owner">
    <x-slot name="title">Create Your Cafe - CafeFlow Onboarding</x-slot>
    <x-slot name="header">Create Your Cafe</x-slot>

    <div class="max-w-4xl mx-auto space-y-8 py-4" x-data="{
        cafeName: '',
        cafeSlug: '',
        imagePath: 'https://images.unsplash.com/photo-1554118811-1e0d58224f24?auto=format&fit=crop&w=1000&q=80',
        updateSlug() {
            this.cafeSlug = this.cafeName.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
        },
        presetImages: [
            'https://images.unsplash.com/photo-1554118811-1e0d58224f24?auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1559925393-8be0ec4767c8?auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=1000&q=80'
        ]
    }">

        {{-- Welcome Header --}}
        <div class="text-center space-y-3">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-accent-50 text-accent-700 border border-accent-200/80 text-xs font-semibold">
                <span>☕</span>
                <span>Cafe Owner Onboarding</span>
            </div>
            <h1 class="font-serif text-3xl sm:text-4xl font-bold text-coffee-950 tracking-tight">
                Create Your Cafe Profile
            </h1>
            <p class="text-sm text-coffee-600 max-w-xl mx-auto leading-relaxed">
                Welcome to CafeFlow, <span class="font-semibold text-coffee-800">{{ $owner->name }}</span>. Register your cafe details and operating schedule to start managing seating capacity and receiving bookings.
            </p>
        </div>

        @if ($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs space-y-1">
                <div class="font-bold flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Please correct the errors below:</span>
                </div>
                <ul class="list-disc list-inside space-y-0.5 text-rose-700 pl-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('owner.cafe.store') }}" class="space-y-8">
            @csrf

            {{-- 1. General Cafe Information --}}
            <div class="dashboard-card space-y-6">
                <div class="pb-3 border-b border-cream-200 flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-accent-50 text-accent-600 flex items-center justify-center font-bold text-sm">1</div>
                    <div>
                        <h2 class="font-serif text-lg font-bold text-coffee-950">Basic Cafe Information</h2>
                        <p class="text-xs text-coffee-500">Your brand name, public URL, and story.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="space-y-1.5 sm:col-span-2">
                        <label for="name" class="block text-xs font-semibold text-coffee-800">Cafe Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" id="name" required
                               x-model="cafeName" @input="updateSlug()"
                               value="{{ old('name') }}"
                               placeholder="e.g. The Velvet Bean"
                               class="w-full text-sm rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400 bg-white placeholder:text-coffee-300">
                    </div>

                    <div class="space-y-1.5">
                        <label for="slug" class="block text-xs font-semibold text-coffee-800">Public Slug / URL Identifier <span class="text-rose-500">*</span></label>
                        <div class="flex rounded-xl shadow-2xs">
                            <span class="inline-flex items-center px-3 rounded-l-xl border border-r-0 border-cream-300 bg-cream-100 text-coffee-500 text-xs font-mono">
                                /cafes/
                            </span>
                            <input type="text" name="slug" id="slug" required
                                   x-model="cafeSlug"
                                   value="{{ old('slug') }}"
                                   placeholder="the-velvet-bean"
                                   class="flex-1 min-w-0 block w-full text-xs font-mono rounded-none rounded-r-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400 bg-white">
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label for="city" class="block text-xs font-semibold text-coffee-800">City / District <span class="text-rose-500">*</span></label>
                        <input type="text" name="city" id="city" required
                               value="{{ old('city', 'Colombo 07') }}"
                               placeholder="e.g. Colombo 07"
                               class="w-full text-sm rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400 bg-white placeholder:text-coffee-300">
                    </div>

                    <div class="space-y-1.5 sm:col-span-2">
                        <label for="address" class="block text-xs font-semibold text-coffee-800">Full Physical Address <span class="text-rose-500">*</span></label>
                        <input type="text" name="address" id="address" required
                               value="{{ old('address') }}"
                               placeholder="e.g. 42 Ward Place, Cinnamon Gardens"
                               class="w-full text-sm rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400 bg-white placeholder:text-coffee-300">
                    </div>

                    <div class="space-y-1.5">
                        <label for="phone" class="block text-xs font-semibold text-coffee-800">Contact Phone Number</label>
                        <input type="text" name="phone" id="phone"
                               value="{{ old('phone') }}"
                               placeholder="e.g. +94 11 234 5678"
                               class="w-full text-sm rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400 bg-white placeholder:text-coffee-300">
                    </div>

                    <div class="space-y-1.5">
                        <label for="email" class="block text-xs font-semibold text-coffee-800">Official Cafe Email</label>
                        <input type="email" name="email" id="email"
                               value="{{ old('email', $owner->email) }}"
                               placeholder="e.g. hello@velvetbean.lk"
                               class="w-full text-sm rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400 bg-white placeholder:text-coffee-300">
                    </div>

                    <div class="space-y-1.5 sm:col-span-2">
                        <label for="description" class="block text-xs font-semibold text-coffee-800">Cafe Story & Atmosphere</label>
                        <textarea name="description" id="description" rows="3"
                                  placeholder="Describe the mood, signature roasts, and seating vibe..."
                                  class="w-full text-sm rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400 bg-white placeholder:text-coffee-300">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- 2. Visual Imagery --}}
            <div class="dashboard-card space-y-6">
                <div class="pb-3 border-b border-cream-200 flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-accent-50 text-accent-600 flex items-center justify-center font-bold text-sm">2</div>
                    <div>
                        <h2 class="font-serif text-lg font-bold text-coffee-950">Cover Image</h2>
                        <p class="text-xs text-coffee-500">Provide an image URL or choose a high-resolution preset.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 items-start">
                    <div class="sm:col-span-2 space-y-3">
                        <label for="image_path" class="block text-xs font-semibold text-coffee-800">Image URL</label>
                        <input type="url" name="image_path" id="image_path"
                               x-model="imagePath"
                               value="{{ old('image_path', 'https://images.unsplash.com/photo-1554118811-1e0d58224f24?auto=format&fit=crop&w=1000&q=80') }}"
                               placeholder="https://images.unsplash.com/..."
                               class="w-full text-xs font-mono rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400 bg-white">

                        <div class="space-y-1.5">
                            <span class="text-[11px] font-semibold text-coffee-600 block">Or pick a curated cafe aesthetic preset:</span>
                            <div class="grid grid-cols-4 gap-2">
                                <template x-for="(img, idx) in presetImages" :key="idx">
                                    <button type="button" @click="imagePath = img"
                                            :class="imagePath === img ? 'ring-2 ring-accent-500 ring-offset-2' : 'opacity-70 hover:opacity-100'"
                                            class="h-14 rounded-xl overflow-hidden border border-cream-200 transition-all">
                                        <img :src="img" alt="Preset" class="w-full h-full object-cover">
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-1.5 text-center">
                        <span class="text-xs font-semibold text-coffee-700 block">Live Preview</span>
                        <div class="h-32 rounded-2xl overflow-hidden border border-cream-200 bg-cream-100 shadow-xs">
                            <img :src="imagePath" alt="Cafe Preview" class="w-full h-full object-cover">
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. Policy & Booking Settings --}}
            <div class="dashboard-card space-y-6">
                <div class="pb-3 border-b border-cream-200 flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-accent-50 text-accent-600 flex items-center justify-center font-bold text-sm">3</div>
                    <div>
                        <h2 class="font-serif text-lg font-bold text-coffee-950">Reservation & Cancellation Policy</h2>
                        <p class="text-xs text-coffee-500">Configure reservation deposit and cancellation deduction.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div class="space-y-1.5">
                        <label for="reservation_fee" class="block text-xs font-semibold text-coffee-800">Table Reservation Fee (LKR) <span class="text-rose-500">*</span></label>
                        <input type="number" step="50" min="0" name="reservation_fee" id="reservation_fee" required
                               value="{{ old('reservation_fee', 500) }}"
                               class="w-full text-sm rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400 bg-white">
                        <p class="text-[11px] text-coffee-500">Paid by customer to secure table booking.</p>
                    </div>

                    <div class="space-y-1.5">
                        <label for="cancellation_penalty_percentage" class="block text-xs font-semibold text-coffee-800">Cancellation Penalty (%) <span class="text-rose-500">*</span></label>
                        <input type="number" step="5" min="0" max="100" name="cancellation_penalty_percentage" id="cancellation_penalty_percentage" required
                               value="{{ old('cancellation_penalty_percentage', 50) }}"
                               class="w-full text-sm rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400 bg-white font-semibold text-rose-700">
                        <p class="text-[11px] text-coffee-500">Standard CafeFlow policy is 50% deduction on cancellation.</p>
                    </div>

                    <div class="space-y-1.5">
                        <label for="status" class="block text-xs font-semibold text-coffee-800">Listing Status <span class="text-rose-500">*</span></label>
                        <select name="status" id="status" required
                                class="w-full text-sm rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400 bg-white">
                            <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active (Visible for Bookings)</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive (Draft)</option>
                        </select>
                        <p class="text-[11px] text-coffee-500">Set to Active to immediately allow customers to discover your cafe.</p>
                    </div>
                </div>
            </div>

            {{-- 4. Weekly Operating Hours --}}
            <div class="dashboard-card space-y-6">
                <div class="pb-3 border-b border-cream-200 flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-accent-50 text-accent-600 flex items-center justify-center font-bold text-sm">4</div>
                    <div>
                        <h2 class="font-serif text-lg font-bold text-coffee-950">Operating Hours (Weekly Schedule)</h2>
                        <p class="text-xs text-coffee-500">Define daily opening and closing hours. Uncheck days that are closed.</p>
                    </div>
                </div>

                @php
                    $days = [
                        1 => 'Monday',
                        2 => 'Tuesday',
                        3 => 'Wednesday',
                        4 => 'Thursday',
                        5 => 'Friday',
                        6 => 'Saturday',
                        7 => 'Sunday',
                    ];
                @endphp

                <div class="space-y-3">
                    @foreach($days as $dayNum => $dayName)
                        <div class="p-3 bg-cream-50/70 rounded-xl border border-cream-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3"
                             x-data="{ isClosed: {{ $dayNum === 7 ? 'false' : 'false' }} }">
                            <input type="hidden" name="hours[{{ $dayNum }}][day_of_week]" value="{{ $dayNum }}">

                            <div class="flex items-center gap-3 sm:w-36">
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox"
                                           name="hours[{{ $dayNum }}][is_closed]"
                                           value="1"
                                           x-model="isClosed"
                                           class="rounded text-rose-600 focus:ring-rose-500 border-cream-300">
                                    <span class="text-xs font-bold text-coffee-900">{{ $dayName }}</span>
                                </label>
                            </div>

                            <div class="flex items-center gap-2 text-xs" x-show="!isClosed">
                                <span class="text-coffee-500">Opens:</span>
                                <input type="time" name="hours[{{ $dayNum }}][opens_at]"
                                       value="{{ old("hours.{$dayNum}.opens_at", '08:00') }}"
                                       class="text-xs rounded-lg border-cream-300 focus:border-accent-500 focus:ring-accent-400 bg-white">

                                <span class="text-coffee-500">Closes:</span>
                                <input type="time" name="hours[{{ $dayNum }}][closes_at]"
                                       value="{{ old("hours.{$dayNum}.closes_at", '21:30') }}"
                                       class="text-xs rounded-lg border-cream-300 focus:border-accent-500 focus:ring-accent-400 bg-white">
                            </div>

                            <div x-show="isClosed" x-cloak class="text-xs text-rose-600 font-semibold italic">
                                Closed all day
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Submit Action Buttons --}}
            <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-4">
                <button type="submit" class="btn-primary w-full sm:w-auto px-8 py-3 text-base shadow-card font-semibold">
                    <span>Create & Launch Cafe Profile</span>
                    <span>&rarr;</span>
                </button>
            </div>
        </form>
    </div>
</x-dashboard-layout>
