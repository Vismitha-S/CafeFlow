<?php

namespace App\Http\Controllers;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OwnerMenuController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display menu categories and items for the owner's cafe.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $owner = $request->user();
        $cafe = $owner->cafe;

        if (! $cafe) {
            return redirect()->route('owner.cafe.create');
        }

        $categories = $cafe->menuCategories()
            ->withCount('menuItems')
            ->orderBy('sort_order', 'asc')
            ->get();

        $selectedCategoryId = $request->integer('category_id');

        $itemsQuery = $cafe->menuItems()
            ->with('category')
            ->orderBy('sort_order', 'asc');

        if ($selectedCategoryId > 0) {
            $itemsQuery->where('menu_category_id', $selectedCategoryId);
        }

        $items = $itemsQuery->get();

        $totalItemsCount = $cafe->menuItems()->count();
        $availableItemsCount = $cafe->menuItems()->where('is_available', true)->where('status', 'active')->count();

        return view('owner.menu.index', compact(
            'owner',
            'cafe',
            'categories',
            'items',
            'selectedCategoryId',
            'totalItemsCount',
            'availableItemsCount',
        ));
    }

    /**
     * Store a new menu category for the owner's cafe.
     */
    public function storeCategory(Request $request): RedirectResponse
    {
        $owner = $request->user();
        $cafe = $owner->cafe;

        if (! $cafe) {
            return redirect()->route('owner.cafe.create');
        }

        $this->authorize('create', MenuCategory::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $cafe->menuCategories()->create($validated);

        return redirect()->route('owner.menu.index')
            ->with('success', "Category '{$validated['name']}' created successfully.");
    }

    /**
     * Update an existing menu category.
     */
    public function updateCategory(Request $request, MenuCategory $menuCategory): RedirectResponse
    {
        $this->authorize('update', $menuCategory);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        $menuCategory->update($validated);

        return redirect()->route('owner.menu.index')
            ->with('success', "Category '{$menuCategory->name}' updated successfully.");
    }

    /**
     * Delete a menu category.
     */
    public function destroyCategory(Request $request, MenuCategory $menuCategory): RedirectResponse
    {
        $this->authorize('delete', $menuCategory);

        $name = $menuCategory->name;
        $menuCategory->delete();

        return redirect()->route('owner.menu.index')
            ->with('success', "Category '{$name}' removed.");
    }

    /**
     * Store a new menu item.
     */
    public function storeItem(Request $request): RedirectResponse
    {
        $owner = $request->user();
        $cafe = $owner->cafe;

        if (! $cafe) {
            return redirect()->route('owner.cafe.create');
        }

        $this->authorize('create', MenuItem::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0'],
            'menu_category_id' => ['nullable', 'exists:menu_categories,id'],
            'image_path' => ['nullable', 'string', 'max:2000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'string', 'in:active,inactive'],
            'is_available' => ['nullable', 'boolean'],
        ]);

        $validated['is_available'] = $request->boolean('is_available', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $cafe->menuItems()->create($validated);

        return redirect()->route('owner.menu.index', ['category_id' => $validated['menu_category_id'] ?? null])
            ->with('success', "Menu item '{$validated['name']}' added successfully.");
    }

    /**
     * Update an existing menu item.
     */
    public function updateItem(Request $request, MenuItem $menuItem): RedirectResponse
    {
        $this->authorize('update', $menuItem);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0'],
            'menu_category_id' => ['nullable', 'exists:menu_categories,id'],
            'image_path' => ['nullable', 'string', 'max:2000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'string', 'in:active,inactive'],
            'is_available' => ['nullable', 'boolean'],
        ]);

        $validated['is_available'] = $request->boolean('is_available');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $menuItem->update($validated);

        return redirect()->route('owner.menu.index', ['category_id' => $menuItem->menu_category_id])
            ->with('success', "Item '{$menuItem->name}' updated successfully.");
    }

    /**
     * Toggle menu item availability.
     */
    public function toggleItemAvailability(Request $request, MenuItem $menuItem): RedirectResponse
    {
        $this->authorize('update', $menuItem);

        $menuItem->update([
            'is_available' => ! $menuItem->is_available,
        ]);

        $stateText = $menuItem->is_available ? 'Available' : 'Unavailable';

        return redirect()->back()
            ->with('success', "Item '{$menuItem->name}' is now {$stateText}.");
    }

    /**
     * Delete a menu item.
     */
    public function destroyItem(Request $request, MenuItem $menuItem): RedirectResponse
    {
        $this->authorize('delete', $menuItem);

        $name = $menuItem->name;
        $menuItem->delete();

        return redirect()->route('owner.menu.index')
            ->with('success', "Item '{$name}' removed.");
    }
}
