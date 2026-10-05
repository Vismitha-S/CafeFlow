<x-dashboard-layout dashboard-role="owner">
    <x-slot name="title">Table Management - {{ $cafe->name }}</x-slot>
    <x-slot name="header">Table Inventory</x-slot>

    <div class="space-y-8" x-data="{
        showAddModal: false,
        showEditModal: false,
        editTable: {
            id: null,
            table_number: '',
            name: '',
            capacity: 2,
            location: 'indoor',
            status: 'active'
        },
        openEdit(t) {
            this.editTable = { ...t };
            this.showEditModal = true;
        }
    }">

        {{-- Top Bar & Action Button --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-cream-200">
            <div>
                <h1 class="font-serif text-2xl sm:text-3xl font-bold text-coffee-950 tracking-tight">
                    Table Inventory & Seating
                </h1>
                <p class="text-xs sm:text-sm text-coffee-500 mt-0.5">
                    Manage table layouts, seating capacities, and active status for <span class="font-semibold text-coffee-800">{{ $cafe->name }}</span>.
                </p>
            </div>

            <button type="button" @click="showAddModal = true" class="btn-primary shrink-0 text-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Add New Table</span>
            </button>
        </div>

        {{-- Inventory KPI Summary Cards --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="dashboard-card p-4">
                <span class="text-[11px] text-coffee-500 block font-medium uppercase tracking-wide">Active Tables</span>
                <p class="font-serif text-2xl font-bold text-coffee-950 mt-1">{{ $activeCount }}</p>
                <p class="text-[11px] text-coffee-400 mt-0.5">out of {{ $tables->count() }} total tables</p>
            </div>
            <div class="dashboard-card p-4">
                <span class="text-[11px] text-coffee-500 block font-medium uppercase tracking-wide">Guest Capacity</span>
                <p class="font-serif text-2xl font-bold text-sage-800 mt-1">{{ $totalCapacity }}</p>
                <p class="text-[11px] text-coffee-400 mt-0.5">seats across active tables</p>
            </div>
            <div class="dashboard-card p-4">
                <span class="text-[11px] text-coffee-500 block font-medium uppercase tracking-wide">Location Split</span>
                <p class="font-serif text-lg font-bold text-coffee-950 mt-1">{{ $indoorCount }} <span class="text-xs font-sans font-normal text-coffee-500">indoor</span></p>
                <p class="text-[11px] text-coffee-400 mt-0.5">{{ $outdoorCount }} outdoor tables</p>
            </div>
        </div>

        {{-- Tables Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($tables as $table)
                @php
                    $displayName = $table->name ?: 'Table ' . $table->table_number;
                    $locationLabel = match($table->location) {
                        'indoor'   => 'Indoor Dining',
                        'outdoor'  => 'Outdoor Terrace',
                        default    => ucfirst($table->location),
                    };
                    $locationIcon = match($table->location) {
                        'indoor'   => '🏠',
                        'outdoor'  => '🌿',
                        default    => '📍',
                    };
                @endphp
                <div class="dashboard-card p-5 flex flex-col gap-4 relative group hover:border-cream-300">
                    {{-- Header row: table number badge + name + status --}}
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-cream-100 border border-cream-200 flex items-center justify-center">
                                <span class="font-serif font-bold text-coffee-800 text-sm leading-none">{{ $table->table_number }}</span>
                            </div>
                            <div class="min-w-0">
                                <h3 class="font-serif text-base font-bold text-coffee-950 leading-tight truncate">
                                    {{ $displayName }}
                                </h3>
                                <p class="text-[11px] text-coffee-400 font-medium mt-0.5">Table #{{ $table->table_number }}</p>
                            </div>
                        </div>
                        <span class="flex-shrink-0 inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $table->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-cream-100 text-coffee-500 border border-cream-200' }}">
                            {{ $table->status === 'active' ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    {{-- Details row --}}
                    <div class="grid grid-cols-2 gap-2">
                        <div class="rounded-lg bg-cream-50 border border-cream-100 px-3 py-2">
                            <p class="text-[10px] text-coffee-400 uppercase tracking-wide font-semibold">Location</p>
                            <p class="text-xs font-semibold text-coffee-800 mt-0.5">{{ $locationIcon }} {{ $locationLabel }}</p>
                        </div>
                        <div class="rounded-lg bg-cream-50 border border-cream-100 px-3 py-2">
                            <p class="text-[10px] text-coffee-400 uppercase tracking-wide font-semibold">Max Guests</p>
                            <p class="text-xs font-semibold text-coffee-800 mt-0.5">👥 {{ $table->capacity }} people</p>
                        </div>
                    </div>

                    {{-- Actions row --}}
                    <div class="flex items-center justify-between pt-3 border-t border-cream-100 gap-2">
                        <form method="POST" action="{{ route('owner.tables.toggle', $table->id) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-[11px] font-semibold {{ $table->status === 'active' ? 'text-amber-700 hover:text-amber-800' : 'text-emerald-700 hover:text-emerald-800' }} transition-colors">
                                {{ $table->status === 'active' ? 'Mark as Inactive' : 'Mark as Active' }}
                            </button>
                        </form>

                        <div class="flex items-center gap-1">
                            <button type="button" @click="openEdit({{ json_encode($table) }})"
                                    class="p-1.5 rounded-lg text-coffee-500 hover:bg-cream-100 hover:text-coffee-900 transition-colors"
                                    title="Edit this table">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </button>
                            <form method="POST" action="{{ route('owner.tables.destroy', $table->id) }}" onsubmit="return confirm('Remove {{ $displayName }}? This cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg text-rose-400 hover:bg-rose-50 hover:text-rose-700 transition-colors" title="Delete this table">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full dashboard-card text-center py-14 px-4 space-y-3">
                    <div class="text-4xl">🪑</div>
                    <p class="font-serif text-lg font-bold text-coffee-900">No tables added yet</p>
                    <p class="text-xs text-coffee-500 max-w-xs mx-auto">Add your first table so guests can start booking reservations at your café.</p>
                    <button type="button" @click="showAddModal = true" class="btn-primary text-xs mt-2">
                        + Add Your First Table
                    </button>
                </div>
            @endforelse
        </div>

        {{-- Add Table Modal --}}
        <div x-show="showAddModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 text-center sm:block sm:p-0">
                <div @click="showAddModal = false" class="fixed inset-0 bg-coffee-950/60 backdrop-blur-xs transition-opacity"></div>

                <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-card transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-cream-200">
                    <form method="POST" action="{{ route('owner.tables.store') }}" class="p-6 space-y-5">
                        @csrf
                        <div class="flex items-center justify-between pb-3 border-b border-cream-100">
                            <h3 class="font-serif text-lg font-bold text-coffee-950">Add New Table</h3>
                            <button type="button" @click="showAddModal = false" class="text-coffee-400 hover:text-coffee-700">✕</button>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label for="add_table_number" class="block text-xs font-semibold text-coffee-800">Table Number <span class="text-rose-500">*</span></label>
                                <input type="number" name="table_number" id="add_table_number" required min="1"
                                       value="{{ old('table_number', (int) ($tables->max('table_number') ?? 0) + 1) }}"
                                       class="w-full text-sm rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400">
                            </div>

                            <div class="space-y-1">
                                <label for="add_capacity" class="block text-xs font-semibold text-coffee-800">Guest Capacity <span class="text-rose-500">*</span></label>
                                <input type="number" name="capacity" id="add_capacity" required min="1" max="50"
                                       value="{{ old('capacity', 4) }}"
                                       class="w-full text-sm rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400">
                            </div>

                            <div class="col-span-2 space-y-1">
                                <label for="add_name" class="block text-xs font-semibold text-coffee-800">Custom Table Name / Label</label>
                                <input type="text" name="name" id="add_name"
                                       placeholder="e.g. Window Nook, Booth 4, Patio Table 2"
                                       class="w-full text-sm rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400">
                            </div>

                            <div class="space-y-1">
                                <label for="add_location" class="block text-xs font-semibold text-coffee-800">Location Area <span class="text-rose-500">*</span></label>
                                <select name="location" id="add_location" required
                                        class="w-full text-sm rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400">
                                    <option value="indoor">Indoor Dining</option>
                                    <option value="outdoor">Outdoor Terrace</option>
                                </select>
                            </div>

                            <div class="space-y-1">
                                <label for="add_status" class="block text-xs font-semibold text-coffee-800">Status <span class="text-rose-500">*</span></label>
                                <select name="status" id="add_status" required
                                        class="w-full text-sm rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400">
                                    <option value="active">Active (Available for booking)</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-3 border-t border-cream-100">
                            <button type="button" @click="showAddModal = false" class="btn-ghost text-xs">Cancel</button>
                            <button type="submit" class="btn-primary text-xs font-semibold">Save Table</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Edit Table Modal --}}
        <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 text-center sm:block sm:p-0">
                <div @click="showEditModal = false" class="fixed inset-0 bg-coffee-950/60 backdrop-blur-xs transition-opacity"></div>

                <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-card transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-cream-200">
                    <form :action="'{{ url('/owner/tables') }}/' + editTable.id" method="POST" class="p-6 space-y-5">
                        @csrf
                        @method('PUT')
                        <div class="flex items-center justify-between pb-3 border-b border-cream-100">
                            <h3 class="font-serif text-lg font-bold text-coffee-950">Edit Table</h3>
                            <button type="button" @click="showEditModal = false" class="text-coffee-400 hover:text-coffee-700">✕</button>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="block text-xs font-semibold text-coffee-800">Table Number <span class="text-rose-500">*</span></label>
                                <input type="number" name="table_number" required min="1"
                                       x-model="editTable.table_number"
                                       class="w-full text-sm rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400">
                            </div>

                            <div class="space-y-1">
                                <label class="block text-xs font-semibold text-coffee-800">Guest Capacity <span class="text-rose-500">*</span></label>
                                <input type="number" name="capacity" required min="1" max="50"
                                       x-model="editTable.capacity"
                                       class="w-full text-sm rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400">
                            </div>

                            <div class="col-span-2 space-y-1">
                                <label class="block text-xs font-semibold text-coffee-800">Custom Table Name / Label</label>
                                <input type="text" name="name"
                                       x-model="editTable.name"
                                       placeholder="e.g. Window Nook"
                                       class="w-full text-sm rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400">
                            </div>

                            <div class="space-y-1">
                                <label class="block text-xs font-semibold text-coffee-800">Location Area <span class="text-rose-500">*</span></label>
                                <select name="location" required x-model="editTable.location"
                                        class="w-full text-sm rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400">
                                    <option value="indoor">Indoor Dining</option>
                                    <option value="outdoor">Outdoor Terrace</option>
                                </select>
                            </div>

                            <div class="space-y-1">
                                <label class="block text-xs font-semibold text-coffee-800">Status <span class="text-rose-500">*</span></label>
                                <select name="status" required x-model="editTable.status"
                                        class="w-full text-sm rounded-xl border-cream-300 focus:border-accent-500 focus:ring-accent-400">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-3 border-t border-cream-100">
                            <button type="button" @click="showEditModal = false" class="btn-ghost text-xs">Cancel</button>
                            <button type="submit" class="btn-primary text-xs font-semibold">Update Table</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-dashboard-layout>
