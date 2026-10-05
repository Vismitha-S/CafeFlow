<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMenuCategoryRequest;
use App\Http\Requests\UpdateMenuCategoryRequest;
use App\Models\Cafe;
use App\Models\MenuCategory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class MenuCategoryController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request, Cafe $cafe)
    {
        $this->authorize('view', $cafe);

        $query = $cafe->menuCategories()->orderBy('sort_order')->orderBy('name');

        if ($request->user()->isCustomer()) {
            $query->where('status', 'active');
        }

        return response()->json($query->get());
    }

    public function show(Request $request, MenuCategory $menuCategory)
    {
        $this->authorize('view', $menuCategory);

        return response()->json($menuCategory);
    }

    public function store(StoreMenuCategoryRequest $request, Cafe $cafe)
    {
        $this->authorize('update', $cafe);

        $category = $cafe->menuCategories()->create($request->validated());

        return response()->json($category, 201);
    }

    public function update(UpdateMenuCategoryRequest $request, MenuCategory $menuCategory)
    {
        $this->authorize('update', $menuCategory);

        $menuCategory->update($request->validated());

        return response()->json($menuCategory);
    }

    public function destroy(Request $request, MenuCategory $menuCategory)
    {
        $this->authorize('delete', $menuCategory);

        // Prevent deletion if it has items? The prompt says "safely detach/reassign items before deletion".
        // Let's detach the items (set menu_category_id to null)
        $menuCategory->items()->update(['menu_category_id' => null]);

        $menuCategory->delete();

        return response()->json(null, 204);
    }
}
