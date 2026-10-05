<?php

namespace App\Http\Controllers;

use App\Models\CafeTable;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OwnerTableController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display the owner's cafe table inventory.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $owner = $request->user();
        $cafe = $owner->cafe;

        if (! $cafe) {
            return redirect()->route('owner.cafe.create');
        }

        $tables = $cafe->tables()
            ->orderBy('table_number', 'asc')
            ->get();

        $activeCount = $tables->where('status', 'active')->count();
        $totalCapacity = (int) $tables->where('status', 'active')->sum('capacity');
        $indoorCount = $tables->where('location', 'indoor')->count();
        $outdoorCount = $tables->where('location', 'outdoor')->count();

        return view('owner.tables.index', compact(
            'owner',
            'cafe',
            'tables',
            'activeCount',
            'totalCapacity',
            'indoorCount',
            'outdoorCount',
        ));
    }

    /**
     * Store a new table for the owner's cafe.
     */
    public function store(Request $request): RedirectResponse
    {
        $owner = $request->user();
        $cafe = $owner->cafe;

        if (! $cafe) {
            return redirect()->route('owner.cafe.create');
        }

        $this->authorize('create', CafeTable::class);

        $validated = $request->validate([
            'table_number' => ['required', 'string', 'max:50'],
            'name' => ['nullable', 'string', 'max:100'],
            'capacity' => ['required', 'integer', 'min:1', 'max:50'],
            'location' => ['required', 'string', 'in:indoor,outdoor'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        $cafe->tables()->create($validated);

        return redirect()->route('owner.tables.index')
            ->with('success', "Table {$validated['table_number']} added successfully.");
    }

    /**
     * Update an existing table for the owner's cafe.
     */
    public function update(Request $request, CafeTable $cafeTable): RedirectResponse
    {
        $this->authorize('update', $cafeTable);

        $validated = $request->validate([
            'table_number' => ['required', 'string', 'max:50'],
            'name' => ['nullable', 'string', 'max:100'],
            'capacity' => ['required', 'integer', 'min:1', 'max:50'],
            'location' => ['required', 'string', 'in:indoor,outdoor'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        $cafeTable->update($validated);

        return redirect()->route('owner.tables.index')
            ->with('success', "Table {$cafeTable->table_number} updated successfully.");
    }

    /**
     * Toggle the active/inactive status of a table.
     */
    public function toggleStatus(Request $request, CafeTable $cafeTable): RedirectResponse
    {
        $this->authorize('update', $cafeTable);

        $newStatus = $cafeTable->status === 'active' ? 'inactive' : 'active';
        $cafeTable->update(['status' => $newStatus]);

        return redirect()->route('owner.tables.index')
            ->with('success', "Table {$cafeTable->table_number} is now {$newStatus}.");
    }

    /**
     * Delete a table from the owner's cafe.
     */
    public function destroy(Request $request, CafeTable $cafeTable): RedirectResponse
    {
        $this->authorize('delete', $cafeTable);

        $tableNumber = $cafeTable->table_number;
        $cafeTable->delete();

        return redirect()->route('owner.tables.index')
            ->with('success', "Table {$tableNumber} removed successfully.");
    }
}
