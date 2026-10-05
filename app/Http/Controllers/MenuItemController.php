<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMenuItemRequest;
use App\Http\Requests\UpdateMenuItemRequest;
use App\Models\Cafe;
use App\Models\MenuItem;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class MenuItemController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request, Cafe $cafe)
    {
        $this->authorize('view', $cafe);

        $query = $cafe->menuItems()->with('category')->orderBy('sort_order')->orderBy('name');

        if ($request->user()->isCustomer()) {
            $query->where('status', 'active')->where('is_available', true);
            // also need to ensure category is active if assigned
            $query->where(function ($q) {
                $q->whereNull('menu_category_id')
                    ->orWhereHas('category', function ($q2) {
                        $q2->where('status', 'active');
                    });
            });
        }

        return response()->json($query->get());
    }

    public function show(Request $request, MenuItem $menuItem)
    {
        $this->authorize('view', $menuItem);
        $menuItem->load('category');

        return response()->json($menuItem);
    }

    public function store(StoreMenuItemRequest $request, Cafe $cafe)
    {
        $this->authorize('update', $cafe);

        $item = $cafe->menuItems()->create($request->validated());

        return response()->json($item, 201);
    }

    public function update(UpdateMenuItemRequest $request, MenuItem $menuItem)
    {
        $this->authorize('update', $menuItem);

        $menuItem->update($request->validated());

        return response()->json($menuItem);
    }

    public function destroy(Request $request, MenuItem $menuItem)
    {
        $this->authorize('delete', $menuItem);

        $menuItem->delete();

        return response()->json(null, 204);
    }
}
