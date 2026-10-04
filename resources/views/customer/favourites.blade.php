{{-- Customer Favourites page matching CafeFlow design reference --}}
<x-dashboard-layout dashboard-role="customer">
    <x-slot name="title">Favourites</x-slot>

    <div class="space-y-8" x-data="{
        favList: {{ json_encode($favourites) }},

        removeFavourite(id) {
            this.favList = this.favList.filter(c => c.id !== id);
        }
    }">

        {{-- Page Header --}}
        <div class="flex items-center justify-between pb-3 border-b border-cream-200">
            <div>
                <h1 class="font-serif text-3xl sm:text-4xl font-bold text-coffee-950 tracking-tight">Saved Favourites</h1>
                <p class="text-xs sm:text-sm text-coffee-500 mt-1">Your personal collection of beloved coffee spots and quiet corners.</p>
            </div>

            <span class="text-xs text-coffee-600 font-medium bg-cream-100 px-3.5 py-1.5 rounded-full border border-cream-200">
                <span class="font-bold text-coffee-900" x-text="favList.length"></span> Saved Cafes
            </span>
        </div>

        {{-- Favourites Grid --}}
        <div x-show="favList.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <template x-for="cafe in favList" :key="cafe.id">
                <div class="cafe-card group flex flex-col h-full bg-white rounded-3xl border border-cream-200 shadow-subtle hover:shadow-card-hover transition-all duration-300">

                    {{-- Image & Heart --}}
                    <div class="relative aspect-4/3 w-full overflow-hidden bg-cream-100 rounded-t-3xl">
                        <img :src="cafe.image" :alt="cafe.name" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                        <div class="absolute inset-0 bg-gradient-to-t from-coffee-950/60 via-transparent to-black/20"></div>

                        {{-- Favourite heart button that allows removal --}}
                        <div class="absolute top-3.5 right-3.5 z-10">
                            <button @click="removeFavourite(cafe.id)"
                                    class="w-9 h-9 rounded-full flex items-center justify-center backdrop-blur-md bg-white/90 text-rose-500 shadow-xs hover:scale-110 active:scale-95 transition-all"
                                    title="Remove from favourites">
                                <svg class="w-4 h-4 fill-rose-500" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                            </button>
                        </div>

                        {{-- Rating pill --}}
                        <div class="absolute bottom-3 left-3.5 z-10">
                            <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg backdrop-blur-md bg-coffee-950/40 border border-white/10 text-white font-medium text-xs">
                                <svg class="w-3.5 h-3.5 text-brass-400 fill-brass-400" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <span class="font-semibold" x-text="cafe.rating"></span>
                                <span class="text-white/70 text-[11px]" x-text="'(' + cafe.reviews_count + ')'"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="p-5 flex flex-col flex-1 justify-between">
                        <div>
                            <h3 class="font-serif text-lg font-bold text-coffee-900 mb-1" x-text="cafe.name"></h3>
                            <p class="flex items-center gap-1.5 text-xs text-coffee-500 mb-3">
                                <svg class="w-3.5 h-3.5 text-accent-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span x-text="cafe.location"></span>
                                <span>•</span>
                                <span class="font-medium text-coffee-700" x-text="cafe.cafe_type"></span>
                            </p>
                            <p class="text-xs text-coffee-500 line-clamp-2 leading-relaxed" x-text="cafe.short_description"></p>
                        </div>

                        {{-- Card footer action --}}
                        <div class="pt-4 border-t border-cream-100 flex items-center justify-between mt-4">
                            <button @click="removeFavourite(cafe.id)" class="text-xs text-rose-500 hover:text-rose-700 font-medium">
                                Remove
                            </button>
                            <a :href="'/cafes/' + cafe.slug"
                               class="btn-primary py-1.5 px-4 text-xs font-semibold">
                                <span>View Cafe</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        {{-- Empty Favourites State --}}
        <div x-show="favList.length === 0" class="text-center py-16 bg-white rounded-3xl border border-cream-200 p-8 space-y-4">
            <div class="w-16 h-16 rounded-full bg-rose-50 text-rose-400 mx-auto flex items-center justify-center">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            </div>
            <h3 class="font-serif text-xl font-bold text-coffee-950">No saved cafes yet</h3>
            <p class="text-xs text-coffee-500 max-w-sm mx-auto">Explore unique cafes and click the heart icon on any card to save your favorite spots here.</p>
            <div class="pt-2">
                <a href="{{ route('customer.explore') }}" class="btn-primary py-2 px-6 text-xs font-semibold">
                    Explore Cafes
                </a>
            </div>
        </div>

    </div>
</x-dashboard-layout>
