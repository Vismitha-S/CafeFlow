<x-dashboard-layout dashboard-role="owner">
    <x-slot name="title">My Cafe - CafeFlow Owner</x-slot>
    <x-slot name="header">My Cafe Management</x-slot>

    <div class="max-w-4xl mx-auto space-y-8" x-data="{
        imagePath: '{{ old('image_path', $cafe->image_path ?: 'https://images.unsplash.com/photo-1554118811-1e0d58224f24?auto=format&fit=crop&w=1000&q=80') }}',
        presetImages: [
            'https://images.unsplash.com/photo-1554118811-1e0d58224f24?auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1559925393-8be0ec4767c8?auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=1000&q=80'
        ]
    }">

        {{-- Page Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-cream-200">
            <div>
                <h1 class="font-serif text-2xl sm:text-3xl font-bold text-coffee-950 tracking-tight">
                    Manage {{ $cafe->name }}
                </h1>
                <p class="text-xs sm:text-sm text-coffee-500 mt-0.5">
                    Update your public cafe profile, contact details, pricing policy, and weekly operating schedule.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('owner.dashboard') }}" class="btn-ghost text-xs">
                    <span>&larr; Back to Dashboard</span>
                </a>
                <a href="{{ route('customer.cafe.show', $cafe->slug) }}" target="_blank" class="btn-secondary text-xs">
                    <span>View Public Page</span>
                    <span>&rarr;</span>
                </a>
                <span class="{{ $cafe->status === 'active' ? 'badge-sage' : 'badge bg-amber-50 text-amber-700' }} text-xs font-semibold px-3 py-1.5">
                    {{ ucfirst($cafe->status) }}
                </span>
            </div>
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

        <form method="POST" action="{{ route('owner.cafe.update') }}" class="space-y-8">
            @csrf
            @method('PUT')

            {{-- 1. General Cafe Information --}}
            <div class="dashboard-card space-y-6">
                <div class="pb-3 border-b border-cream-200 flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-accent-50 text-accent-600 flex items-center justify-center font-bold text-sm">1</div>
                    <div>
                        <h2 class="font-serif text-lg font-bold text-coffee-950">Cafe Profile & Identity</h2>
                        <p class="text-xs text-coffee-500">Public presentation, name, and address.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="space-y-1.5 sm:col-span-2">
                        <label for="name" class="block text-xs font-semibold text-coffee-800">Cafe Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" id="name" required
                               value="{{ old('name', $cafe->name) }}"
                               class="w-full text-sm rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400 bg-white">
                    </div>

                    <div class="space-y-1.5">
                        <label for="slug" class="block text-xs font-semibold text-coffee-800">Slug / URL Identifier <span class="text-rose-500">*</span></label>
                        <input type="text" name="slug" id="slug" required
                               value="{{ old('slug', $cafe->slug) }}"
                               class="w-full text-xs font-mono rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400 bg-white">
                    </div>

                    <div class="space-y-1.5">
                        <label for="city" class="block text-xs font-semibold text-coffee-800">City / District <span class="text-rose-500">*</span></label>
                        <input type="text" name="city" id="city" required
                               value="{{ old('city', $cafe->city) }}"
                               class="w-full text-sm rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400 bg-white">
                    </div>

                    <div class="space-y-1.5 sm:col-span-2">
                        <label for="address" class="block text-xs font-semibold text-coffee-800">Physical Address <span class="text-rose-500">*</span></label>
                        <input type="text" name="address" id="address" required
                               value="{{ old('address', $cafe->address) }}"
                               class="w-full text-sm rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400 bg-white">
                    </div>

                    <div class="space-y-1.5">
                        <label for="phone" class="block text-xs font-semibold text-coffee-800">Phone Number</label>
                        <input type="text" name="phone" id="phone"
                               value="{{ old('phone', $cafe->phone) }}"
                               class="w-full text-sm rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400 bg-white">
                    </div>

                    <div class="space-y-1.5">
                        <label for="email" class="block text-xs font-semibold text-coffee-800">Official Email</label>
                        <input type="email" name="email" id="email"
                               value="{{ old('email', $cafe->email) }}"
                               class="w-full text-sm rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400 bg-white">
                    </div>

                    <div class="space-y-1.5 sm:col-span-2">
                        <label for="description" class="block text-xs font-semibold text-coffee-800">Cafe Story & Atmosphere</label>
                        <textarea name="description" id="description" rows="3"
                                  class="w-full text-sm rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400 bg-white">{{ old('description', $cafe->description) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- 2. Imagery --}}
            <div class="dashboard-card space-y-6">
                <div class="pb-3 border-b border-cream-200 flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-accent-50 text-accent-600 flex items-center justify-center font-bold text-sm">2</div>
                    <div>
                        <h2 class="font-serif text-lg font-bold text-coffee-950">Cover Image</h2>
                        <p class="text-xs text-coffee-500">Update cafe banner image.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 items-start">
                    <div class="sm:col-span-2 space-y-3">
                        <label for="image_path" class="block text-xs font-semibold text-coffee-800">Image URL</label>
                        <input type="url" name="image_path" id="image_path"
                               x-model="imagePath"
                               value="{{ old('image_path', $cafe->image_path) }}"
                               placeholder="https://..."
                               class="w-full text-xs font-mono rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400 bg-white">

                        <div class="space-y-1.5">
                            <span class="text-[11px] font-semibold text-coffee-600 block">Or pick a curated preset:</span>
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
                        <span class="text-xs font-semibold text-coffee-700 block">Preview</span>
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
                        <h2 class="font-serif text-lg font-bold text-coffee-950">Pricing & Status Settings</h2>
                        <p class="text-xs text-coffee-500">Configure reservation deposit, cancellation policy, and listing visibility.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div class="space-y-1.5">
                        <label for="reservation_fee" class="block text-xs font-semibold text-coffee-800">Reservation Fee (LKR) <span class="text-rose-500">*</span></label>
                        <input type="number" step="50" min="0" name="reservation_fee" id="reservation_fee" required
                               value="{{ old('reservation_fee', $cafe->reservation_fee) }}"
                               class="w-full text-sm rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400 bg-white font-bold text-coffee-900">
                    </div>

                    <div class="space-y-1.5">
                        <label for="cancellation_penalty_percentage" class="block text-xs font-semibold text-coffee-800">Cancellation Penalty (%) <span class="text-rose-500">*</span></label>
                        <input type="number" step="5" min="0" max="100" name="cancellation_penalty_percentage" id="cancellation_penalty_percentage" required
                               value="{{ old('cancellation_penalty_percentage', $cafe->cancellation_penalty_percentage ?? 50) }}"
                               class="w-full text-sm rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400 bg-white font-semibold text-rose-700">
                    </div>

                    <div class="space-y-1.5">
                        <label for="status" class="block text-xs font-semibold text-coffee-800">Status <span class="text-rose-500">*</span></label>
                        <select name="status" id="status" required
                                class="w-full text-sm rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400 bg-white">
                            <option value="active" {{ old('status', $cafe->status) === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $cafe->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- 4. Operating Hours --}}
            <div class="dashboard-card space-y-6">
                <div class="pb-3 border-b border-cream-200 flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-accent-50 text-accent-600 flex items-center justify-center font-bold text-sm">4</div>
                    <div>
                        <h2 class="font-serif text-lg font-bold text-coffee-950">Operating Hours (Weekly Schedule)</h2>
                        <p class="text-xs text-coffee-500">Configure daily opening and closing hours.</p>
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
                        @php
                            $hourRecord = $hours->get($dayNum);
                            $opens = $hourRecord ? \Carbon\Carbon::parse($hourRecord->opens_at)->format('H:i') : '08:00';
                            $closes = $hourRecord ? \Carbon\Carbon::parse($hourRecord->closes_at)->format('H:i') : '21:30';
                            $isClosedDefault = $hourRecord ? ($hourRecord->is_closed ? 'true' : 'false') : 'false';
                        @endphp
                        <div class="p-3 bg-cream-50/70 rounded-xl border border-cream-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3"
                             x-data="{ isClosed: {{ old("hours.{$dayNum}.is_closed") ? 'true' : $isClosedDefault }} }">
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
                                       value="{{ old("hours.{$dayNum}.opens_at", $opens) }}"
                                       class="text-xs rounded-lg border-cream-300 focus:border-accent-500 focus:ring-accent-400 bg-white">

                                <span class="text-coffee-500">Closes:</span>
                                <input type="time" name="hours[{{ $dayNum }}][closes_at]"
                                       value="{{ old("hours.{$dayNum}.closes_at", $closes) }}"
                                       class="text-xs rounded-lg border-cream-300 focus:border-accent-500 focus:ring-accent-400 bg-white">
                            </div>

                            <div x-show="isClosed" x-cloak class="text-xs text-rose-600 font-semibold italic">
                                Closed all day
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('owner.dashboard') }}" class="btn-secondary px-6 py-3 font-semibold text-xs">
                    Cancel
                </a>
                <button type="submit" class="btn-primary px-8 py-3 shadow-card font-semibold">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</x-dashboard-layout>
