<x-dashboard-layout dashboard-role="owner">
    <x-slot name="title">Menu Management - {{ $cafe->name }}</x-slot>
    <x-slot name="header">Menu Management</x-slot>

    <div class="space-y-8" x-data="{
        showAddCatModal: false,
        showEditCatModal: false,
        showAddItemModal: false,
        showEditItemModal: false,
        editCat: { id: null, name: '', description: '', sort_order: 0, status: 'active' },
        editItem: { id: null, name: '', description: '', price: 0, menu_category_id: '', is_available: true, status: 'active', image_path: '', sort_order: 0 },
        openEditCat(c) {
            this.editCat = { ...c };
            this.showEditCatModal = true;
        },
        openEditItem(item) {
            this.editItem = { ...item };
            this.showEditItemModal = true;
        }
    }">

        {{-- Top Header & Action Buttons --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-cream-200">
            <div>
                <h1 class="font-serif text-2xl sm:text-3xl font-bold text-coffee-950 tracking-tight">
                    Menu & Offerings
                </h1>
                <p class="text-xs sm:text-sm text-coffee-500 mt-0.5">
                    Curate your artisanal beverages, pastries, and main courses for <span class="font-semibold text-coffee-800">{{ $cafe->name }}</span>.
                </p>
            </div>

            <div class="flex items-center gap-2.5">
                <button type="button" @click="showAddCatModal = true" class="btn-secondary text-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Add Category</span>
                </button>
                <button type="button" @click="showAddItemModal = true" class="btn-primary text-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Add Menu Item</span>
                </button>
            </div>
        </div>

        {{-- KPI Stat Bar --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="dashboard-card p-4">
                <span class="text-[11px] text-coffee-500 block font-medium">Total Menu Items</span>
                <p class="font-serif text-2xl font-bold text-coffee-950 mt-1">{{ $totalItemsCount }}</p>
            </div>
            <div class="dashboard-card p-4">
                <span class="text-[11px] text-coffee-500 block font-medium">Currently Available</span>
                <p class="font-serif text-2xl font-bold text-sage-800 mt-1">{{ $availableItemsCount }} <span class="text-xs font-sans text-coffee-400 font-normal">Active</span></p>
            </div>
            <div class="dashboard-card p-4">
                <span class="text-[11px] text-coffee-500 block font-medium">Menu Categories</span>
                <p class="font-serif text-2xl font-bold text-coffee-950 mt-1">{{ $categories->count() }}</p>
            </div>
            <div class="dashboard-card p-4">
                <span class="text-[11px] text-coffee-500 block font-medium">Unavailable Items</span>
                <p class="font-serif text-2xl font-bold text-rose-700 mt-1">{{ $totalItemsCount - $availableItemsCount }}</p>
            </div>
        </div>

        {{-- Categories Scroll Bar / Filter --}}
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="font-serif text-base font-bold text-coffee-950">Categories</h3>
                <span class="text-xs text-coffee-500">{{ $categories->count() }} registered</span>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('owner.menu.index') }}"
                   class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all {{ !$selectedCategoryId ? 'bg-coffee-900 text-white shadow-xs' : 'bg-white border border-cream-300 text-coffee-700 hover:bg-cream-100' }}">
                    All Offerings ({{ $totalItemsCount }})
                </a>

                @foreach($categories as $cat)
                    <div class="inline-flex items-center rounded-xl border border-cream-300 overflow-hidden text-xs transition-all {{ $selectedCategoryId === $cat->id ? 'bg-coffee-900 text-white border-coffee-900 shadow-xs' : 'bg-white hover:bg-cream-50' }}">
                        <a href="{{ route('owner.menu.index', ['category_id' => $cat->id]) }}" class="px-3 py-1.5 font-semibold {{ $selectedCategoryId === $cat->id ? 'text-white' : 'text-coffee-800' }}">
                            {{ $cat->name }} <span class="{{ $selectedCategoryId === $cat->id ? 'text-accent-300' : 'text-coffee-400' }}">({{ $cat->menu_items_count }})</span>
                        </a>
                        <button type="button" @click="openEditCat({{ json_encode($cat) }})" class="px-1.5 py-1.5 border-l border-cream-200/50 hover:bg-black/10 transition-colors" title="Edit Category">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Menu Items List --}}
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="font-serif text-base font-bold text-coffee-950">
                    Menu Items {{ $selectedCategoryId ? 'in Selected Category' : '' }}
                </h3>
                <span class="text-xs text-coffee-500">{{ $items->count() }} items shown</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($items as $item)
                    <div class="dashboard-card p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 group hover:border-cream-300">
                        <div class="flex items-start gap-3.5 min-w-0 flex-1">
                            @if($item->image_path)
                                <div class="w-16 h-16 rounded-xl overflow-hidden bg-cream-100 shrink-0 border border-cream-200">
                                    <img src="{{ $item->image_path }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                                </div>
                            @else
                                <div class="w-16 h-16 rounded-xl bg-cream-100 text-coffee-500 shrink-0 flex items-center justify-center border border-cream-200 font-serif text-lg font-bold">
                                    {{ substr($item->name, 0, 1) }}
                                </div>
                            @endif

                            <div class="min-w-0 space-y-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h4 class="font-serif text-sm font-bold text-coffee-950 truncate">{{ $item->name }}</h4>
                                    @if($item->category)
                                        <span class="text-[10px] text-accent-700 bg-accent-50 border border-accent-200/60 px-2 py-0.5 rounded-full font-medium">
                                            {{ $item->category->name }}
                                        </span>
                                    @endif
                                </div>
                                <p class="text-[11px] text-coffee-500 line-clamp-2 leading-relaxed">{{ $item->description }}</p>
                                <p class="font-serif text-xs font-bold text-coffee-900">LKR {{ number_format($item->price, 2) }}</p>
                            </div>
                        </div>

                        <div class="flex sm:flex-col items-center sm:items-end justify-between w-full sm:w-auto pt-2 sm:pt-0 border-t sm:border-t-0 border-cream-100 gap-2 shrink-0">
                            {{-- Availability toggle form --}}
                            <form method="POST" action="{{ route('owner.menu.items.toggle', $item->id) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-[11px] font-semibold px-2.5 py-1 rounded-full transition-colors {{ $item->is_available ? 'bg-sage-100 text-sage-800 hover:bg-sage-200' : 'bg-rose-100 text-rose-800 hover:bg-rose-200' }}">
                                    {{ $item->is_available ? '● Available' : '○ Unavailable' }}
                                </button>
                            </form>

                            <div class="flex items-center gap-1.5">
                                <button type="button" @click="openEditItem({{ json_encode($item) }})" class="p-1.5 rounded-lg text-coffee-600 hover:bg-cream-100 hover:text-coffee-900" title="Edit Item">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </button>

                                <form method="POST" action="{{ route('owner.menu.items.destroy', $item->id) }}" onsubmit="return confirm('Are you sure you want to delete {{ $item->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-rose-500 hover:bg-rose-50 hover:text-rose-700" title="Delete Item">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full dashboard-card text-center py-12 px-4 space-y-3">
                        <p class="font-serif text-lg font-bold text-coffee-900">No menu items found</p>
                        <p class="text-xs text-coffee-500 max-w-sm mx-auto">Add specialty coffee, breakfast dishes, or artisanal desserts to your offerings.</p>
                        <button type="button" @click="showAddItemModal = true" class="btn-primary text-xs mt-2">
                            Add Menu Item
                        </button>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Add Category Modal --}}
        <div x-show="showAddCatModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 text-center sm:block sm:p-0">
                <div @click="showAddCatModal = false" class="fixed inset-0 bg-coffee-950/60 backdrop-blur-xs"></div>

                <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-card transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-cream-200">
                    <form method="POST" action="{{ route('owner.menu.categories.store') }}" class="p-6 space-y-4">
                        @csrf
                        <div class="flex items-center justify-between pb-3 border-b border-cream-100">
                            <h3 class="font-serif text-lg font-bold text-coffee-950">Add Menu Category</h3>
                            <button type="button" @click="showAddCatModal = false" class="text-coffee-400 hover:text-coffee-700">✕</button>
                        </div>

                        <div class="space-y-1">
                            <label class="block text-xs font-semibold text-coffee-800">Category Name <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" required placeholder="e.g. Specialty Brews, Pastries"
                                   class="w-full text-sm rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400">
                        </div>

                        <div class="space-y-1">
                            <label class="block text-xs font-semibold text-coffee-800">Description</label>
                            <textarea name="description" rows="2" placeholder="Optional brief summary..."
                                      class="w-full text-sm rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="space-y-1">
                                <label class="block text-xs font-semibold text-coffee-800">Sort Order</label>
                                <input type="number" name="sort_order" value="0" min="0"
                                       class="w-full text-sm rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400">
                            </div>
                            <div class="space-y-1">
                                <label class="block text-xs font-semibold text-coffee-800">Status <span class="text-rose-500">*</span></label>
                                <select name="status" required class="w-full text-sm rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-3 border-t border-cream-100">
                            <button type="button" @click="showAddCatModal = false" class="btn-ghost text-xs">Cancel</button>
                            <button type="submit" class="btn-primary text-xs font-semibold">Save Category</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Edit Category Modal --}}
        <div x-show="showEditCatModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 text-center sm:block sm:p-0">
                <div @click="showEditCatModal = false" class="fixed inset-0 bg-coffee-950/60 backdrop-blur-xs"></div>

                <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-card transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-cream-200">
                    <form :action="'{{ url('/owner/menu/categories') }}/' + editCat.id" method="POST" class="p-6 space-y-4">
                        @csrf
                        @method('PUT')
                        <div class="flex items-center justify-between pb-3 border-b border-cream-100">
                            <h3 class="font-serif text-lg font-bold text-coffee-950">Edit Category</h3>
                            <button type="button" @click="showEditCatModal = false" class="text-coffee-400 hover:text-coffee-700">✕</button>
                        </div>

                        <div class="space-y-1">
                            <label class="block text-xs font-semibold text-coffee-800">Category Name <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" required x-model="editCat.name"
                                   class="w-full text-sm rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400">
                        </div>

                        <div class="space-y-1">
                            <label class="block text-xs font-semibold text-coffee-800">Description</label>
                            <textarea name="description" rows="2" x-model="editCat.description"
                                      class="w-full text-sm rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="space-y-1">
                                <label class="block text-xs font-semibold text-coffee-800">Sort Order</label>
                                <input type="number" name="sort_order" x-model="editCat.sort_order" min="0"
                                       class="w-full text-sm rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400">
                            </div>
                            <div class="space-y-1">
                                <label class="block text-xs font-semibold text-coffee-800">Status <span class="text-rose-500">*</span></label>
                                <select name="status" required x-model="editCat.status" class="w-full text-sm rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-3 border-t border-cream-100">
                            <button type="button" @click="showEditCatModal = false" class="btn-ghost text-xs">Cancel</button>
                            <button type="submit" class="btn-primary text-xs font-semibold">Update Category</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Add Menu Item Modal --}}
        <div x-show="showAddItemModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 text-center sm:block sm:p-0">
                <div @click="showAddItemModal = false" class="fixed inset-0 bg-coffee-950/60 backdrop-blur-xs"></div>

                <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-card transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-cream-200">
                    <form method="POST" action="{{ route('owner.menu.items.store') }}" class="p-6 space-y-4">
                        @csrf
                        <div class="flex items-center justify-between pb-3 border-b border-cream-100">
                            <h3 class="font-serif text-lg font-bold text-coffee-950">Add Menu Item</h3>
                            <button type="button" @click="showAddItemModal = false" class="text-coffee-400 hover:text-coffee-700">✕</button>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2 space-y-1">
                                <label class="block text-xs font-semibold text-coffee-800">Item Name <span class="text-rose-500">*</span></label>
                                <input type="text" name="name" required placeholder="e.g. Iced Vanilla Oat Latte"
                                       class="w-full text-sm rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400">
                            </div>

                            <div class="space-y-1">
                                <label class="block text-xs font-semibold text-coffee-800">Category <span class="text-rose-500">*</span></label>
                                <select name="menu_category_id" required class="w-full text-sm rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ $selectedCategoryId === $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="space-y-1">
                                <label class="block text-xs font-semibold text-coffee-800">Price (LKR) <span class="text-rose-500">*</span></label>
                                <input type="number" step="50" min="0" name="price" required placeholder="850.00"
                                       class="w-full text-sm rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400">
                            </div>

                            <div class="col-span-2 space-y-1">
                                <label class="block text-xs font-semibold text-coffee-800">Description</label>
                                <textarea name="description" rows="2" placeholder="Ingredients, flavor notes, artisan details..."
                                          class="w-full text-sm rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400"></textarea>
                            </div>

                            <div class="col-span-2 space-y-1">
                                <label class="block text-xs font-semibold text-coffee-800">Item Image URL</label>
                                <input type="url" name="image_path" placeholder="https://images.unsplash.com/..."
                                       class="w-full text-xs font-mono rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400">
                            </div>

                            <div class="space-y-1">
                                <label class="block text-xs font-semibold text-coffee-800">Status <span class="text-rose-500">*</span></label>
                                <select name="status" required class="w-full text-sm rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>

                            <div class="space-y-1 flex items-center pt-5">
                                <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-semibold text-coffee-800">
                                    <input type="checkbox" name="is_available" value="1" checked class="rounded text-accent-600 focus:ring-accent-500 border-cream-300">
                                    <span>Available for ordering</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-3 border-t border-cream-100">
                            <button type="button" @click="showAddItemModal = false" class="btn-ghost text-xs">Cancel</button>
                            <button type="submit" class="btn-primary text-xs font-semibold">Save Item</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Edit Menu Item Modal --}}
        <div x-show="showEditItemModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 text-center sm:block sm:p-0">
                <div @click="showEditItemModal = false" class="fixed inset-0 bg-coffee-950/60 backdrop-blur-xs"></div>

                <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-card transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-cream-200">
                    <form :action="'{{ url('/owner/menu/items') }}/' + editItem.id" method="POST" class="p-6 space-y-4">
                        @csrf
                        @method('PUT')
                        <div class="flex items-center justify-between pb-3 border-b border-cream-100">
                            <h3 class="font-serif text-lg font-bold text-coffee-950">Edit Menu Item</h3>
                            <button type="button" @click="showEditItemModal = false" class="text-coffee-400 hover:text-coffee-700">✕</button>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2 space-y-1">
                                <label class="block text-xs font-semibold text-coffee-800">Item Name <span class="text-rose-500">*</span></label>
                                <input type="text" name="name" required x-model="editItem.name"
                                       class="w-full text-sm rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400">
                            </div>

                            <div class="space-y-1">
                                <label class="block text-xs font-semibold text-coffee-800">Category <span class="text-rose-500">*</span></label>
                                <select name="menu_category_id" required x-model="editItem.menu_category_id" class="w-full text-sm rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="space-y-1">
                                <label class="block text-xs font-semibold text-coffee-800">Price (LKR) <span class="text-rose-500">*</span></label>
                                <input type="number" step="50" min="0" name="price" required x-model="editItem.price"
                                       class="w-full text-sm rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400">
                            </div>

                            <div class="col-span-2 space-y-1">
                                <label class="block text-xs font-semibold text-coffee-800">Description</label>
                                <textarea name="description" rows="2" x-model="editItem.description"
                                          class="w-full text-sm rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400"></textarea>
                            </div>

                            <div class="col-span-2 space-y-1">
                                <label class="block text-xs font-semibold text-coffee-800">Item Image URL</label>
                                <input type="url" name="image_path" x-model="editItem.image_path"
                                       class="w-full text-xs font-mono rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400">
                            </div>

                            <div class="space-y-1">
                                <label class="block text-xs font-semibold text-coffee-800">Status <span class="text-rose-500">*</span></label>
                                <select name="status" required x-model="editItem.status" class="w-full text-sm rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>

                            <div class="space-y-1 flex items-center pt-5">
                                <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-semibold text-coffee-800">
                                    <input type="checkbox" name="is_available" value="1" x-model="editItem.is_available" class="rounded text-accent-600 focus:ring-accent-500 border-cream-300">
                                    <span>Available for ordering</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-3 border-t border-cream-100">
                            <button type="button" @click="showEditItemModal = false" class="btn-ghost text-xs">Cancel</button>
                            <button type="submit" class="btn-primary text-xs font-semibold">Update Item</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-dashboard-layout>
